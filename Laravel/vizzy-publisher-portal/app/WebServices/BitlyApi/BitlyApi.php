<?php

namespace App\WebServices\BitlyApi;

use Throwable;
use Illuminate\Support\Facades\Http;

class BitlyApi
{
    protected $accessToken;
    protected $customDomain;
    protected $groupGuid;

    /**
     * Constructor
     *
     * @param string $accessToken
     * @param string $customDomain
     * @param string $groupGuid
     */
    public function __construct($accessToken, $customDomain, $groupGuid)
    {
        $this->accessToken = $accessToken;
        $this->customDomain = $customDomain;
        $this->groupGuid = $groupGuid;
    }


    /**
     * Generate Bitlink
     */
    public function generateBitlink($url, $audio)
    {
       try {
            $tags =  [config('app.env') . '-audio-' . $audio->id];

            $response = Http::accept('application/json')
                ->withToken($this->accessToken)
                ->post('https://api-ssl.bitly.com/v4/bitlinks', [
                    "long_url" => $url,
                    "domain" => $this->customDomain,
                    "group_guid" => $this->groupGuid,
                    "tags" => $tags
                ]);

            $body = $response->json();

            return $body['link'];
        } catch (Throwable $e) {
            return 'invalid url';
        }
    }

    /**
     * Delete Bitlink
     */
    public function deleteBitlink($bitlink)
    {
       try {
            $response = Http::accept('application/json')
                ->withToken($this->accessToken)
                ->delete('https://api-ssl.bitly.com/v4/bitlinks/'.$bitlink);
        } catch (Throwable $e) {
        }
    }

}
