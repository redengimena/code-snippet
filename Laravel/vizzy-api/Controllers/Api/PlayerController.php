<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lukaswhite\PodcastFeedParser\Parser;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;
use App\WebServices\PodcastIndex\PodcastIndex;
use App\Models\Played;
use App\Models\Favourited;


class PlayerController extends Controller
{
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
     * Player
     */
    protected function episode(Request $request)
    {
        $id = $request->query('id');
        if (strpos($id, '|') > -1) {
            [$feed_url, $guid] = explode('|', $id);
        } else {
            return $this->sendError('Invalid id', 200);
        }

        $vizzy = $this->vizzyPodcaster->getVizzy($id);
        if (!$vizzy) {
            $podcast = $this->podcastIndex->getPodcastByFeedURL($feed_url);
            $episodes = $this->podcastIndex->getEpisodesByFeedURL($feed_url);
            foreach ($episodes as $ep) {
                if ($ep['guid'] == $guid) {
                    $vizzy = [
                        'podcast' => [
                            'feed_url' => $feed_url,
                            'title' => html_entity_decode($podcast['title']),
                            'description' => html_entity_decode($podcast['intro']),
                            'language' => $podcast['language'],
                            'author' => $podcast['publisher'],
                            'episodes_count' => $podcast['episode_count'],
                            'artwork' => $podcast['image'],
                            'categories' => $podcast['categories']
                        ],
                        'episode' => [
                            'guid' => $guid,
                            'pub_date' => $ep['pub_date']->format('m/d/Y'),
                            'title' => html_entity_decode($ep['title']),
                            'description' => html_entity_decode($ep['description']),
                            'ep_num' => $ep['ep_num'],
                            'duration' => \Str::duration($ep['duration']),
                            'artwork' => $ep['image'] ? $ep['image'] : $podcast['image'],
                            'audio_url' => $ep['audio_url'],
                            'has_vizzy' => false
                        ],
                    ];
                    $vizzy = json_decode(json_encode($vizzy), FALSE);

                    break;
                }
            }

            if (!$vizzy) {
                return $this->sendError('Episode does not exist', 200);
            }
        }

        $played = Played::where('user_id', $request->user()->id)
            ->where('feed_url', $feed_url)
            ->where('episode_guid', $guid)
            ->first();
        if ($played) {
            $vizzy->elapsed = $played->elapsed;
        } else {
            $vizzy->elapsed = 0;
        }

        $show_favourited = false;
        $episode_favourited = false;
        $favourites = Favourited::where('user_id', $request->user()->id)
            ->where('feed_url', $feed_url)
            ->get();
        foreach ($favourites as $favourite) {
            if ($favourite->type == 'show') {
                $show_favourited = true;
            } elseif($favourite->episode_guid == $guid) {
                $episode_favourited = true;
            }
        }

        $vizzy->favourited = [
            'show' => $show_favourited,
            'episode' => $episode_favourited
        ];

        return $this->sendResponse($vizzy);
    }
}