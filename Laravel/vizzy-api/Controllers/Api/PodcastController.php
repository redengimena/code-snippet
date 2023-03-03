<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lukaswhite\PodcastFeedParser\Parser;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;
use App\WebServices\PodcastIndex\PodcastIndex;
use App\Models\Played;
use App\Models\Favourited;
use App\Traits\UrlGetContentsTrait;

class PodcastController extends Controller
{
    use UrlGetContentsTrait;

    protected $vizzyPodcaster;
    protected $podcastIndex;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VizzyPodcaster $vizzyPodcaster, PodcastIndex $podcastIndex)
    {
        $this->vizzyPodcaster = $vizzyPodcaster;
        $this->podcastIndex = $podcastIndex;
    }

    /**
     * podcast homepage
     */
    protected function show(Request $request)
    {

        $id = $request->query('id');
        $sort = $request->query('sort');
        $filter = $request->query('filter');
        $data = [];

        // Check show/episode favourited
        $show_favourited = false;
        $episode_favourited = [];
        $favourited = Favourited::where('user_id', $request->user()->id)->where('feed_url', $id)->get();
        foreach ($favourited as $fav) {
            if ($fav->type == 'show') {
                $show_favourited = true;
            } else {
                $episode_favourited[] = $fav->episode_guid;
            }
        }

        // Check episode played
        $episode_played = [];
        $played = Played::where('user_id', $request->user()->id)->where('feed_url', $id)->get();
        foreach ($played as $p) {
            $episode_played[$p->episode_guid] = $p->elapsed;
        }

        // Check has vizzy
        $episode_vizzied = $this->vizzyPodcaster->getAllVizzyGuidByPodcast($id);

        // Get podcast detail from api
        $podcast = $this->podcastIndex->getPodcastByFeedURL($id);

        $data['podcast'] = [
            'feed_url' => $id,
            'title' => html_entity_decode($podcast['title']),
            'publisher' => html_entity_decode($podcast['publisher']),
            'image' => $podcast['image'],
            'following' => $show_favourited,
            'intro' => html_entity_decode(strip_tags($podcast['intro'])),
            'categories' => $podcast['categories']
        ];

        $episodes = $this->podcastIndex->getEpisodesByFeedURL($id);
        $data['episodes'] = [];

        foreach ($episodes as $episode) {

            if ($filter == 'vizzy') {
                if (!in_array($episode['guid'], $episode_vizzied)){
                    continue;
                }
            } elseif ($filter == 'novizzy') {
                if (in_array($episode['guid'], $episode_vizzied)){
                    continue;
                }
            } elseif ($filter == 'played') {
                if (!array_key_exists($episode['guid'], $episode_played)){
                    continue;
                }
            } elseif ($filter == 'unplayed') {
                if (array_key_exists($episode['guid'], $episode_played)){
                    continue;
                }
            }

            $data['episodes'][] = [
                'guid' => $episode['guid'],
                'pub_date' => $episode['pub_date']->format('m/d/Y'),
                'title' => html_entity_decode($episode['title']),
                'description' => \Str::limit(html_entity_decode(strip_tags($episode['description'])), 300, '...'),
                'artwork' => $episode['image'] ? $episode['image'] : $podcast['image'],
                'ep_num' => $episode['ep_num'],
                'duration' => \Str::duration($episode['duration']),
                'played' => array_key_exists($episode['guid'], $episode_played) ? $episode_played[$episode['guid']] : 0,
                'favourited' => in_array($episode['guid'], $episode_favourited),
                'has_vizzy' => in_array($episode['guid'], $episode_vizzied)
            ];
        }

        if ($sort == 'oldest') {
            $data['episodes'] = array_reverse($data['episodes']);
        }

        return $this->sendResponse($data);
    }

}