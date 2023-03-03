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


class TopShowController extends Controller
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
     * Display a listing of top show selected.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $urls = [];
        $topshows = TopShow::orderBy('order')->get();
        foreach ($topshows as $topshow) {
            $urls[] = $topshow->podcast->feed_url;
        }
        $podcasturls = "['" . implode("','",$urls). "']";

        return view('admin.top-shows.index', compact('topshows','podcasturls'));
    }

    /**
     * Add top show.
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
        }

        $topshow = TopShow::where('podcast_id', $podcast->id)->first();
        if ($topshow) {
            return json_encode(['success' => false]);
        }

        $order = TopShow::max('order');
        $topshow = new TopShow();
        $topshow->podcast_id = $podcast->id;
        $topshow->order = $order ? $order+1 : 1;
        $topshow->save();

        Cache::forget('topshow_'.$topshow->id);

        return json_encode(['success' => true, 'data' => $topshow->podcast->toArray()]);
    }


    /**
     * Delete top show.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $topshow = TopShow::where('podcast_id', $request->input('id'))->first();
        if ($topshow) {
            $feed_url = $topshow->podcast->feed_url;
            $order = $topshow->order;
            try {
              DB::beginTransaction();

              $topshow->delete();
              DB::table('top_shows')->where('order', '>', $order)
                  ->update(['order' => DB::raw('`order` - 1')]);

              DB::commit();
            } catch (\Exception $e) {
              DB::rollback();

              return json_encode(['success' => false, 'data' => $e->getMessage()]);
            }
        }

        return json_encode(['success' => true, 'data' => $feed_url]);
    }

    /**
     * Re-order top show.
     *
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request)
    {
        $records = $request->input('data');

        $cases = [];
        $params = [];

        foreach ($records as $order => $record) {
            $cases[] = "WHEN {$record['id']} then ?";
            $params[] = $order+1;
        }

        $cases = implode(' ', $cases);

        try {
            DB::update("UPDATE top_shows SET `order` = CASE `podcast_id` {$cases} END", $params);

        } catch (\Exception $e) {
            return json_encode(['success' => false, 'data' => $e->getMessage()]);
        }

        return json_encode(['success' => true]);
    }

}
