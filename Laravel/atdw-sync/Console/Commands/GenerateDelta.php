<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class GenerateDelta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atdw:delta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate delta file from ATDW';

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client(['base_uri' => 'https://atlas.atdw-online.com.au/api/atlas/']);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $output_folder = config('atdw.output_folder');
        $delta = date('Y-m-d 00:00:00');

        $url = "products?key=373f973560dd4754b9b2cdf5c3638890&cats=ACCOMM&out=json".
               "&fl=product_id,product_name,product_description,product_short_description,product_image,product_classifications,address,boundary,comms,rate_from,rate_to,number_of_rooms,deal_type,product_attribute,product_accessibility_attribute,product_external_system,status,product_update_date".
               "&order=product_update_date".
               "&delta=".urlencode($delta);

        $data = $this->fetchDeltaJSON($url);
        file_put_contents($output_folder.'/accomm-delta.json', json_encode($data));

        // $url = "https://atlas.atdw-online.com.au/api/atlas/products?key=373f973560dd4754b9b2cdf5c3638890&term=Crowne Plaza Surfers Paradise&out=json".
        //         "&fl=product_id,product_name,product_description,product_short_description,product_image,product_classifications,address,boundary,comms,rate_from,rate_to,number_of_rooms,deal_type,product_attribute,product_accessibility_attribute,product_external_system,status,product_update_date";
        // $utf16_json = $this->getUrlContent($url);
        // $json = iconv('UTF-16LE', 'UTF-8', $utf16_json);
        // file_put_contents($output_folder.'/debug.json', $json);

    }

    /**
     * Helper function to do get request
     */
    protected function getUrlContent($url)
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return '[]';
        }

        return $response->getBody()->getContents();
    }

    /**
     * Get delta from ATDW API using JSON
     */
    protected function fetchDeltaJSON($url, $page=1, $size=500)
    {
        $next_url = $url . '&size='. $size . '&pge=' . $page;
        $utf16_json = $this->getUrlContent($next_url);
        $json = iconv('UTF-16LE', 'UTF-8', $utf16_json);
        $data = json_decode($json);

        // transform product data
        foreach ($data->products as $product) {
            $this->transformProductJSON($product);
        }

        if (($page * $size) < $data->numberOfResults) {
            $page++;
            $next_data = $this->fetchDeltaJSON($url, $page, $size);
            foreach ($next_data->products as $product) {
                array_push($data->products, $product);
            }
        }

        return $data;
    }

    /**
     * Convert product json to be consumable by WP all import
     */
    protected function transformProductJSON(&$product) {

        # convert address
        $address = [];
        foreach ($product->addresses as $idx => $item) {
            if ($item->address_type == 'PHYSICAL') {
                $line = $item->address_line;
                if ($item->address_line2) {
                    $line .= ' '.$item->address_line2;
                }
                $address[] = $line;
                $address[] = $item->city.' '.$item->state.' '.$item->postcode ;
                $address[] = $item->country;
                $state_array = [];
                $region = $item->region[0] ?? "";
                $state_array = $this->state_mapping($item->state);
                $product->region = $state_array.">". $region;
                break;
            }
        }
        unset($product->addresses);
        $product->address = implode(', ', $address);

        # convert product attribute to pipe separated list
        $attributes = [];
        foreach ($product->attributes as $idx => $attribute) {
          $type = $attribute->attributeTypeIdDescription;
          $value = $attribute->attributeIdDescription;

          # combine entity facility and service facility
          if (in_array($type, ['Entity Facility', 'Service Facility'])) {
              $type = "Facility";
          }

          if (!array_key_exists($type, $attributes)) {
              $attributes[$type] = $value;
          } else {
              $attributes[$type] .= '|'.$value;
          }
        }
        $product->attributes = $attributes;

        # convert accessibility attribute to pipe separated list
        $attributes = [];
        foreach ($product->accessibilityAttributes as $idx => $attribute) {
          $type = $attribute->attributeTypeIdDescription;
          $value = $attribute->attributeSubType1IdDescription;
          if (!array_key_exists($type, $attributes)) {
              $attributes[$type] = $value;
          } else {
              $attributes[$type] .= '|'.$value;
          }
        }
        $product->accessibilityAttributes = $attributes;

        # convert external systems
        $externals = [];
        foreach ($product->externalsystems as $idx => $item) {
          $type = $item->externalSystemCode;
          $value = $item->externalSystemText;
          if (!array_key_exists($type, $attributes)) {
              $externals[$type] = $value;
          } else {
              $externals[$type] .= '|'.$value;
          }
        }
        $product->externalsystems = $externals;

        #latitude, longitude
        if ($product->boundary) {
            list($lat, $long) = explode(',', $product->boundary);
            $product->latitude = $lat;
            $product->longitude = $long;
        }

        # classification - category
        $category = [];
        foreach ($product->productClassifications as $item) {
            $category[] = $this->category_mapping($item);
        }
        unset($product->productClassifications);
        $product->categories = implode('|', $category);

        # Transforming size of image
        if ($product->productImage) {
            $url = $product->productImage;
            $urlData = parse_url($url);
            parse_str($urlData['query'], $query);
            $query['w'] = 1920;
            unset($query['h']);
            $urlData['query'] = http_build_query($query);
            $product->productImage = $urlData['scheme'] . '://' . $urlData['host'] . $urlData['path'] . '?' . $urlData['query'];
        }
    }

    protected function category_mapping($key)
    {
        $mapping =  [
            'APARTMENT' =>	'Apartments',
            'BACKPACKER' => 'Backpackers and Hostels',
            'BEDBREAKFA' => 'Bed and Breakfast',
            'VANCAMP' => 'Caravan, Camping and Holiday Parks',
            'CABCOTTAGE' => 'Cottages',
            'FARMSTAY' => 'Farmstays',
            'HOLHOUSE' => 'Holiday Houses',
            'HOTEL' => 'Hotels',
            'MOTEL' => 'Motels',
            'RESORT' => 'Resorts',
            'RETREAT' => 'Retreat and Lodges'
        ];

        if (array_key_exists($key, $mapping)) {
            return $mapping[$key];
        }

        return '';
    }

    protected function state_mapping($key)
    {
        $mapping = [
            'ACT' => 'Australian Capital Territory',
            'NSW' => 'New South Wales',
            'NT' => 'Northern Territory',
            'TAS' => 'Tasmania',
            'SA' => 'South Australia',
            'QLD' => 'Queensland',
            'WA' => 'Western Australia',
            'VIC' => 'Victoria'
        ];
        
        if (array_key_exists($key, $mapping)){
            return $mapping[$key];
        }

        return '';
    }
}
