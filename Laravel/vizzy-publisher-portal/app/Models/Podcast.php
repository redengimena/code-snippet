<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lukaswhite\PodcastFeedParser\Parser;
use Illuminate\Support\Facades\Cache;
use App\Traits\UrlGetContentsTrait;

class Podcast extends Model
{
    use UrlGetContentsTrait;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function categories() {
        return $this->hasMany(PodcastsCategory::class);
    }

    public function vizzy() {
        return $this->hasMany(Vizzy::class);
    }

    public function getVizzyByGuid($guid) {
        return Vizzy::where('podcast_id', $this->id)->where('episode_guid', $guid)->first();
    }

    public function getCategoriesNameAttribute() {
        $categories = [];
        foreach ($this->categories as $category) {
            $categories[] = $category->category;
        }

        return implode(', ', $categories);
    }

    public function accessible(User $user) {
        return $this->user_id == $user->id;
    }

    public function rss() {
        // $rss = Cache::get('podcastrss'.$this->feed_url);
        // if (!$rss){
            $content = $this->url_get_contents($this->feed_url);

            $parser = new Parser();
            $parser->setContent($content);
            $rss = $parser->run();
        //     Cache::put('podcastrss'.$this->feed_url, $rss, now()->addMinutes(60*12));
        // }

        return $rss;
    }
}
