<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Vizzy;
use App\Models\Podcast;
use App\Models\TopShow;
use App\Models\PodcastsCategory;
use App\Http\Resources\Vizzy as VizzyResource;
use App\Http\Resources\TopShow as TopShowResource;
use Lukaswhite\PodcastFeedParser\Parser;
use App\Traits\UrlGetContentsTrait;

class VizzyController extends ApiController
{
    use UrlGetContentsTrait;

    /**
     * Get latest vizzys
     *
     * @param GuzzleHttp\Request
     */
    public function latestVizzys(Request $request) {
        $limit = $request->query('limit') && ctype_digit($request->query('limit')) ? $request->query('limit') : 10;
        $offset = $request->query('offset') && ctype_digit($request->query('offset')) ? $request->query('offset') : 0;

        if ($request->query('categories')) {
            $categories = explode('|',$request->query('categories'));
            $vizzys = Vizzy::whereHas('podcast.categories', function (Builder $query) use ($categories){
                // $query->whereIn('category', $categories);

                $query->Where(function ($query) use($categories) {
                  for ($i = 0; $i < count($categories); $i++){
                      $query->orwhere('category', 'like',  '%' . $categories[$i] .'%');
                  }
                });

            })->where('status', Vizzy::STATUS_PUBLISHED)->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
        } else {
            $vizzys = Vizzy::where('status', Vizzy::STATUS_PUBLISHED)->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
        }

        return $this->sendResponse(VizzyResource::collection($vizzys));
    }

    /**
     * Get latest shows with vizzys
     *
     * @param GuzzleHttp\Request
     */
    public function latestShowsWithVizzys(Request $request) {
        $limit = $request->query('limit') && ctype_digit($request->query('limit')) ? $request->query('limit') : 10;
        $offset = $request->query('offset') && ctype_digit($request->query('offset')) ? $request->query('offset') : 0;
        $data = [];

        if ($request->query('categories')) {
            $categories = explode('|',$request->query('categories'));
            $podcasts = Podcast::whereHas('categories', function (Builder $query) use ($categories){
                $query->Where(function ($query) use($categories) {
                  for ($i = 0; $i < count($categories); $i++){
                      $query->orwhere('category', 'like',  '%' . $categories[$i] .'%');
                  }
                });
            })->whereHas('vizzy', function($query) {
                return $query->where('status', Vizzy::STATUS_PUBLISHED);
            })->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
        } else {
            $podcasts = Podcast::whereHas('vizzy', function($query) {
                return $query->where('status', Vizzy::STATUS_PUBLISHED);
            })->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
        }
        foreach ($podcasts as $podcast) {
            $data[] = [
              'url' => $podcast->feed_url,
              'title' => $podcast->title,
              'image' => $podcast->image
            ];
        }

        return $this->sendResponse($data);
    }


    /**
     * Get top shows
     *
     * @param GuzzleHttp\Request
     */
    public function topShows(Request $request) {
        $limit = $request->query('limit') && ctype_digit($request->query('limit')) ? $request->query('limit') : 10;
        $offset = $request->query('offset') && ctype_digit($request->query('offset')) ? $request->query('offset') : 0;

        $topShows = TopShow::orderBy('order')->offset($offset)->limit($limit)->get();

        return $this->sendResponse(TopShowResource::collection($topShows));
    }

    /**
     * Get top shows with vizzy
     *
     * @param GuzzleHttp\Request
     */
    public function topVizzyShows(Request $request) {
        $limit = $request->query('limit') && ctype_digit($request->query('limit')) ? $request->query('limit') : 10;
        $offset = $request->query('offset') && ctype_digit($request->query('offset')) ? $request->query('offset') : 0;

        $topVizzyShows = TopShow::select('top_shows.*')
            ->join('vizzies', 'vizzies.podcast_id','=','top_shows.podcast_id')
            ->where('vizzies.status', Vizzy::STATUS_PUBLISHED)
            ->orderBy('order')->offset($offset)->limit($limit)->get();

        return $this->sendResponse(TopShowResource::collection($topVizzyShows));
    }

    /**
     * Get vizzy data
     *
     * @param GuzzleHttp\Request
     */
    public function show(Request $request) {
        $id = $request->query('id');
        if (strpos($id, '|') > -1) {
            [$feed_url, $guid] = explode('|', $id);
        } else {
            return $this->sendError('Invalid id', 200);
        };

        $vizzy = Vizzy::select('vizzies.*')
                ->where('episode_guid', $guid)
                ->where('status', Vizzy::STATUS_PUBLISHED)
            ->join('podcasts', 'podcasts.id', '=', 'vizzies.podcast_id')
            ->where('podcasts.feed_url', $feed_url)
            ->first();
        if ($vizzy) {
            $vizzyResource = new VizzyResource($vizzy);
            if (!$vizzyResource) {
                return $this->sendError('Episode does not exist.');
            }
            return $this->sendResponse($vizzyResource);
        }

        return $this->sendError();

        // when vizzy not found, pull data from rss feed url
        $episode_data = Cache::get('episode_'.$id);
        if (!$episode_data) {

            $content = $this->url_get_contents($feed_url);
            $parser = new Parser();
            $parser->setContent($content);
            $rss = $parser->run();

            $episode = $rss->getEpisodes()->findByGuid($guid);

            if (!$episode) {
                return $this->sendError('Episode does not exist.');
            }

            $ep_num = '';
            if ($episode->getSeason()){
                $ep_num .= 'S'.$episode->getSeason();
            }
            if ($episode->getEpisodeNumber()){
                $ep_num .= 'E'.$episode->getEpisodeNumber();
            }

            $categories = array_unique(array_map(function($n) { return html_entity_decode($n->getName()); }, $rss->getCategories()));

            $episode_data = [
                'podcast' => [
                    'feed_url' => $feed_url,
                    'title' => html_entity_decode($rss->getTitle()),
                    'description' => html_entity_decode($rss->getDescription()),
                    'language' => $rss->getLanguage(),
                    'author' => $rss->getAuthor(),
                    'episodes_count' => count($rss->getEpisodes()),
                    'artwork' => $rss->getArtwork()->getUri(),
                    'categories' => $categories
                ],
                'episode' => [
                    'guid' => $guid,
                    'pub_date' => $episode->getPublishedDate()->format('m/d/Y'),
                    'title' => html_entity_decode($episode->getTitle()),
                    'description' => html_entity_decode($episode->getDescription()),
                    'ep_num' => $ep_num,
                    'duration' => \Str::duration($episode->getDuration()),
                    'artwork' => $episode->getArtwork() ? $episode->getArtwork()->getUri() : $rss->getArtwork()->getUri(),
                    'audio_url' => $episode->getMedia()->getUri(),
                    'has_vizzy' => false
                ],
            ];

            Cache::put('episode_'.$id, $episode_data, now()->addMinutes(60*12));
        }

        return $this->sendResponse($episode_data);

    }

    /**
     * Check show/episode has vizzy
     *
     * @param GuzzleHttp\Request
     */
    public function hasVizzy(Request $request) {
        $id = $request->query('id');
        if (strpos($id, '|') > -1) {
            [$feed_url, $guid] = explode('|', $id);
            $vizzy = Vizzy::select('vizzies.*')
                ->where('episode_guid', $guid)
                ->where('status', Vizzy::STATUS_PUBLISHED)
                ->join('podcasts', 'podcasts.id', '=', 'vizzies.podcast_id')
                ->where('podcasts.feed_url', $feed_url)
                ->first();
            return $this->sendResponse($vizzy ? true : false);
        } else {
            $feed_url = $id;
            $vizzy = Vizzy::select('vizzies.*')
                ->join('podcasts', 'podcasts.id', '=', 'vizzies.podcast_id')
                ->where('podcasts.feed_url', $feed_url)
                ->where('vizzies.status', Vizzy::STATUS_PUBLISHED)
                ->first();
            return $this->sendResponse($vizzy ? true : false);
        };
    }

    /**
     * Get all vizzys of podcast
     *
     * @param GuzzleHttp\Request
     */
    public function getAllVizzyGuidByPodcast(Request $request) {
        $id = $request->query('id');

        $vizzy_guids = [];
        $vizzy = Vizzy::select('vizzies.episode_guid')
            ->join('podcasts', 'podcasts.id', '=', 'vizzies.podcast_id')
            ->where('podcasts.feed_url', $id)
            ->where('vizzies.status', Vizzy::STATUS_PUBLISHED)
            ->get();
        foreach($vizzy as $v) {
          $vizzy_guids[] = $v->episode_guid;
        }

        return $this->sendResponse($vizzy_guids);
    }

    /**
     * Return vizzy categories
     *
     * @param GuzzleHttp\Request
     */
    public function vizzyCategories(Request $request) {

        $data = PodcastsCategory::select('category')
            ->join('vizzies', 'vizzies.podcast_id', '=', 'podcasts_categories.podcast_id')
            ->where('vizzies.status', Vizzy::STATUS_PUBLISHED)
            ->groupBy('category')->get();

        return $this->sendResponse($data);

    }
}