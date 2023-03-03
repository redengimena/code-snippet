<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Str;

class TopShow extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $topshow_data = Cache::get('topshow_'.$this->id);
        if (!$topshow_data) {

            $podcast = $this->podcast;
            $rss = $this->podcast->rss();

            $topshow_data = [
                'id' => $podcast->feed_url,
                'feed_url' => $podcast->feed_url,
                'title' => $podcast->title,
                'description' => strip_tags($podcast->description),
                'language' => $rss->getLanguage(),
                'author' => $rss->getAuthor(),
                'episodes_count' => count($rss->getEpisodes()),
                'artwork' => $podcast->image
            ];

            Cache::put('topshow_'.$this->id, $topshow_data, now()->addMinutes(60*12));
        }

        return $topshow_data;

    }

}