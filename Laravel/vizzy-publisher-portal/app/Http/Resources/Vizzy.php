<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Str;

class Vizzy extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $vizzy_data = Cache::get('vizzy_'.$this->id);
        if (!$vizzy_data) {
            $podcast = $this->podcast;
            $rss = $this->podcast->rss();
            $episode = $rss->getEpisodes()->findByGuid($this->episode_guid);

            if (!$episode) {
                return false;
            }

            $cards = $this->generateCardJson();

            $ep_num = '';
            if ($episode->getSeason()){
                $ep_num .= 'S'.$episode->getSeason();
            }
            if ($episode->getEpisodeNumber()){
                $ep_num .= 'E'.$episode->getEpisodeNumber();
            }

            $categories = [];
            foreach ($podcast->categories as $category) {
                $categories[] = $category->category;
            }

            $vizzy_data = [
                'id' => $this->id,
                'image' => $this->image,
                'podcast' => [
                    'feed_url' => $podcast->feed_url,
                    'title' => $podcast->title,
                    'description' => $podcast->description,
                    'language' => $rss->getLanguage(),
                    'author' => $rss->getAuthor(),
                    'episodes_count' => count($rss->getEpisodes()),
                    'artwork' => $podcast->image,
                    'categories' => $categories
                ],
                'episode' => [
                    'guid' => $this->episode_guid,
                    'pub_date' => $episode->getPublishedDate()->format('m/d/Y'),
                    'title' => $this->title,
                    'description' => html_entity_decode($episode->getDescription()),
                    'ep_num' => $ep_num,
                    'duration' => Str::duration($episode->getDuration()),
                    'artwork' => $episode->getArtwork() ? $episode->getArtwork()->getUri() : $podcast->image,
                    'audio_url' => $this->audio_url ? $this->audio_url : $episode->getMedia()->getUri(),
                    'has_vizzy' => true
                ],
                'cards' => $cards
            ];

            Cache::put('vizzy_'.$this->id, $vizzy_data, now()->addMinutes(60*12));
        }

        return $vizzy_data;

    }

    public function generateCardJson()
    {
        $output = [];
        foreach ($this->cards as $card) {
            $output[] = [
                'card_id' => $card->id,
                'start' => $card->start,
                'end' => $card->end,
                'image' => $card->image,
                'interactions' => $this->generateCardInteractionJson($card)
            ];
        }

        return $output;
        // return json_decode($this->content);

    }

    public function generateCardInteractionJson($card)
    {
        return [
            'info' => $this->generateCardInteractionInfoJson($card->info),
            'social' => $this->generateCardInteractionSocialJson($card->social),
            'web' => $this->generateCardInteractionWebJson($card->web),
            'product' => $this->generateCardInteractionProductJson($card->product),
        ];
    }

    public function generateCardInteractionInfoJson($infos)
    {
        if (!$infos->count()) {
            return false;
        }

        $info = $infos->first();
        return [
            'icon' => Storage::disk('s3')->url('icons/info.png'),
            'data' => [
                'image' => $info->image,
                'title' => $info->title,
                'content' => $info->content,
            ]
        ];
    }

    public function generateCardInteractionSocialJson($socialGroup)
    {
        if (!$socialGroup->count()) {
            return false;
        }

        $output = [];
        $icon = '';
        foreach ($socialGroup as $group) {
            $links = [];
            foreach ($group->links as $link) {
                $links[] = [
                    'url' => $link->url,
                    'type' => $link->type,
                    'icon' => Storage::disk('s3')->url('icons/'.$link->type.'.png')
                ];

                if (!$icon) {
                    $icon = ($link->type == 'other') ? 'social' : $link->type;
                } else {
                    $icon = ($icon != $link->type) ? 'social' : $icon;
                }
            }
            $output[] = [
                'title' => $group->title,
                'links' => $links
            ];
        }

        return [
            'icon' => Storage::disk('s3')->url('icons/'.$icon.'.png'),
            'data' => $output
        ];

    }

    public function generateCardInteractionWebJson($webGroup)
    {
        if (!$webGroup->count()) {
            return false;
        }

        $output = [];
        $icon = '';
        foreach ($webGroup as $group) {
            $links = [];
            foreach ($group->links as $link) {
                $links[] = [
                    'url' => $link->url,
                    'type' => $link->type,
                    'icon' => Storage::disk('s3')->url('icons/'.$link->type.'.png')
                ];

                if (!$icon) {
                    $icon = ($link->type == 'other') ? 'web' : $link->type;
                } else {
                    $icon = ($icon != $link->type) ? 'web' : $icon;
                }
            }
            $output[] = [
                'title' => $group->title,
                'content' => $group->content,
                'image' => $group->image,
                'links' => $links
            ];
        }

        return [
            'icon' => Storage::disk('s3')->url('icons/'.$icon.'.png'),
            'data' => $output
        ];

    }

    public function generateCardInteractionProductJson($products)
    {
        if (!$products->count()) {
            return false;
        }

        $product = $products->first();

        $icon = ($product->type != 'other') ? $product->type : 'product';

        return [
            'icon' => Storage::disk('s3')->url('icons/' .$icon. '.png'),
            'data' => [
                'type' => $product->type,
                'image' => $product->image,
                'title' => $product->title,
                'content' => $product->content,
                'url' => $product->url
            ]
        ];
    }
}