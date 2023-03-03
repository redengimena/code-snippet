<?php

namespace App\WebServices\PodcastIndexApi;

use Carbon\Carbon;
use PodcastIndex\Client;
use Illuminate\Support\Facades\Cache;

class PodcastIndexApi
{
    protected $client;

    /**
     * Constructor
     *
     * @param string $baseApiUrl
     */
    public function __construct($apiKey, $apiSecret)
    {
        $this->client = new Client([
            'app' => 'AppName',
            'key' => $apiKey,
            'secret' => $apiSecret
        ]);        
    }

    /**
     * Search Podcast by keyword/url
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function searchPodcast($keyword = null, $feed_url = null)
    {
        if ($keyword) {
            $searchResult = Cache::get('podcaseindex'.$keyword);
            if (!$searchResult) {
                $searchResult = $this->client->search->byTerm($keyword)->json();
                Cache::put('podcaseindex'.$keyword, $searchResult, now()->addMinutes(1440));
            }            
        } else {
            $searchResult = Cache::get('podcaseindex'.$feed_url);            
            if (!$searchResult) {
                try {
                    $searchResult = $this->client->podcasts->byFeedUrl($feed_url)->json();
                }
                catch (RequestException $e) {
                    if ($e->hasResponse()){
                        if ($e->getResponse()->getStatusCode() == '400') {
                            $searchResult = new \stdClass();
                            $searchResult->feeds = [];
                        }
                    }
                }
                Cache::put('podcaseindex'.$feed_url, $searchResult, now()->addMinutes(1440));
            }
        }

        return $searchResult;
    }


    /**
     * Get all categories
     */
    public function getCategories()
    {
        $result = Cache::get('podcaseindex-categories');
        if (!$result) {
            $result = $this->client->get('/categories/list')->json();
            Cache::put('podcaseindex-categories', $result, now()->addMinutes(1440));
        }        
        return $result->feeds;
    } 

}
