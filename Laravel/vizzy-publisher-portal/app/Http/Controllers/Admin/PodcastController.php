<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\TopShow;
use App\Models\Podcast;
use App\Models\PodcastsCategory;
use Lukaswhite\PodcastFeedParser\Parser;
use App\Traits\UrlGetContentsTrait;


class PodcastController extends Controller
{
    use UrlGetContentsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    /**
     * Display a listing of podcast claimed by admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $urls = [];
        $podcasts = Podcast::where('user_id', 0)->orderBy('id', 'desc')->get();
        foreach ($podcasts as $podcast) {
            $urls[] = $podcast->feed_url;
        }
        $podcasturls = "['" . implode("','",$urls). "']";

        return view('admin.podcasts.index', compact('podcasts','podcasturls'));
    }

    /**
     * Add podcast.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $feed_url = $request->input('feed_url');

        // check podcast already added to system
        $podcast = Podcast::where('feed_url', $feed_url)->first();
        if (!$podcast) {
            $content = $this->url_get_contents($feed_url);
            $parser = new Parser();
            $parser->setContent($content);
            $rss = $parser->run();

            $podcast = new Podcast();
            $podcast->user_id = 0;
            $podcast->title = html_entity_decode($rss->getTitle());
            $podcast->description = html_entity_decode(strip_tags($rss->getDescription()));
            $podcast->image = $rss->getArtwork()->getUri();
            $podcast->feed_owner_email = $rss->getOwner()->getEmail();
            $podcast->feed_url = $feed_url;
            $podcast->episodes = $rss->getEpisodes()->count();
            $podcast->save();

            $categories = array_unique(array_map(function($n) { return html_entity_decode($n->getName()); }, $rss->getCategories()));
            foreach ($categories as $category) {
                $podcastCategory = new PodcastsCategory();
                $podcastCategory->podcast()->associate($podcast);
                $podcastCategory->category = $category;
                $podcastCategory->save();
            }

            return redirect(route('admin.podcasts.index'))->with('success', 'Podcast added.');
        }

        return back()->with('error', '\'' .$podcast->title. '\' already claimed by owner.');


    }

}
