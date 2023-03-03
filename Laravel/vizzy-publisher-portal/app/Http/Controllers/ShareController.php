<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\RequestException;
use App\WebServices\VizzyApi\VizzyApi;
use Lukaswhite\PodcastFeedParser\Parser;
use App\Models\Vizzy;
use App\Traits\UrlGetContentsTrait;

class ShareController extends Controller
{
    use UrlGetContentsTrait;

    protected $vizzyApi;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VizzyApi $vizzyApi)
    {
        $this->vizzyApi = $vizzyApi;
    }


    /**
     * Share page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function share(Request $request, $slug)
    {
        $data = Cache::get('share_'.$slug);
        if (!$data) {
            $data = $this->vizzyApi->getEpisodeByShareSlug($slug);
            if (!$data) {
                return abort(404);
            }
            Cache::put('share_'.$slug, $data);
        }

        $rss = Cache::get('podcastrss'.$data->feed_url);
        if (!$rss){
            $content = $this->url_get_contents($data->feed_url);
            $parser = new Parser();
            $parser->setContent($content);
            $rss = $parser->run();
            Cache::put('podcastrss'.$data->feed_url, $rss, now()->addMinutes(60*12));
        }

        if ($data->episode_guid) {
            $episode = $rss->getEpisodes()->findByGuid($data->episode_guid);
        } else {
            $episode = null;
        }

        $vizzy = Vizzy::select('vizzies.*')
                ->where('episode_guid', $data->episode_guid)
            ->join('podcasts', 'podcasts.id', '=', 'vizzies.podcast_id')
            ->where('podcasts.feed_url', $data->feed_url)
            ->first();

        return view('share.snippet', compact('rss', 'episode', 'vizzy'));
    }

}
