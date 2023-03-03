<?php

namespace App\WebServices\VizzyApi;

use Carbon\Carbon;
use GuzzleHttp\Client;

class VizzyApi
{
    protected $baseApiUrl;
    protected $client;

    /**
     * Constructor
     *
     * @param string $baseApiUrl
     */
    public function __construct($baseApiUrl, $apiClientId, $apiClientSecret)
    {
        $this->baseApiUrl = $baseApiUrl;
        $this->apiClientId = $apiClientId;
        $this->apiClientSecret = $apiClientSecret;
        $this->client = new Client(['base_uri' => $this->getBaseApiUrl()]);
        $this->accessToken = null;
        $this->accessTokenExpires = null;
    }

    /**
     * Getter for baseApiUrl
     *
     * @return string
     */
    public function getBaseApiUrl()
    {
        return $this->baseApiUrl;
    }

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        if (!$this->accessToken || $this->isAccessTokenExpired()) {
            try {
                $response = $this->client->request('POST', '/oauth/token', [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->apiClientId,
                        'client_secret' => $this->apiClientSecret,
                    ]]);

            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return $e->getMessage();
                // return $e->getResponse()->getStatusCode();
            }

            $content = json_decode($response->getBody()->getContents());
            $this->accessToken = $content->access_token;
            $this->accessTokenExpires = Carbon::now()->timestamp + $content->expires_in;

        }
        return $this->accessToken;
    }

    /**
     *  Check if accestoken expired
     */
    public function isAccessTokenExpired()
    {
        if ($this->accessTokenExpires && ($this->accessTokenExpires > Carbon::now()->timestamp)) {
            return False;
        }

        return True;
    }

    /**
     * Get request header
     */
    public function getApiHeader() {
        return [
            'cache-control' => 'no-cache',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
    }

    /**
     * Get all podcast categories
     *
     * @return GuzzleHttp\Request
     */
    public function getPodcastCategories()
    {
        try {
            $response = $this->client->request('GET', 'podcast-categories', ['headers' => $this->getApiHeader()]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            //return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return $result->data;
        }
    }

    /**
     * Get single podcast category
     *
     * @return GuzzleHttp\Request
     */
    public function getPodcastCategory($id)
    {
        try {
            $response = $this->client->request('GET', 'podcast-category/'.$id, ['headers' => $this->getApiHeader()]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            //return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return $result->data;
        }
    }

    /**
     * Update single podcast category
     *
     * @return GuzzleHttp\Request
     */
    public function updatePodcastCategory($id, $data)
    {
        try {
            $response = $this->client->request('POST', 'podcast-category/'.$id, [
                'headers' => $this->getApiHeader(),
                'json' => $data
            ]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return $result->data;
        } else {
            return false;
        }
    }

    /**
     * create a podcast category
     *
     * @return GuzzleHttp\Request
     */
    public function storePodcastCategory($data)
    {
        try {
            $response = $this->client->request('POST', 'podcast-category', [
                'headers' => $this->getApiHeader(),
                'json' => $data
            ]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return $result->data;
        } else {
            return false;
        }
    }

    /**
     * delete a podcast category
     *
     * @return GuzzleHttp\Request
     */
    public function deletePodcastCategory($id)
    {
        try {
            $response = $this->client->request('DELETE', 'podcast-category/'.$id, [
                'headers' => $this->getApiHeader()
            ]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // return $e->getMessage();
            return false;
        }

        return $result->success;
    }

    /**
     * Get episode by share slug
     *
     * @return GuzzleHttp\Request
     */
    public function getEpisodeByShareSlug($slug)
    {
        try {
            $response = $this->client->request('POST', 'share-slug', [
              'headers' => $this->getApiHeader(),
              'json' => ['slug' => $slug]
            ]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            //return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return $result->data;
        }
    }

    /**
     * publish of vizzy
     *
     * @return GuzzleHttp\Request
     */
    public function publish($episode_guid)
    {
        try {
            $response = $this->client->request('POST', 'vizzy/published', [
                'headers' => $this->getApiHeader(),
                'json' => ['id' => $episode_guid]
            ]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * unpublish of vizzy
     *
     * @return GuzzleHttp\Request
     */
    public function unpublish($episode_guid)
    {
        try {
            $response = $this->client->request('POST', 'vizzy/unpublished', [
                'headers' => $this->getApiHeader(),
                'json' => ['id' => $episode_guid]
            ]);
            $result = json_decode($response->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            // return $e->getMessage();
            return false;
        }

        if ($result->success) {
            return true;
        } else {
            return false;
        }
    }

}
