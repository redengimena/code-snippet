<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;
use App\WebServices\PodcastIndex\PodcastIndex;
use Lukaswhite\PodcastFeedParser\Parser;
use App\Http\Resources\PodcastCategorySimple as PodcastCategoryResource;
use App\Models\PodcastCategory;
use App\Models\Favourited;
use App\Models\Played;
use App\Traits\UrlGetContentsTrait;

class IndexController extends Controller
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

    public function index(Request $request)
    {
        $data = [
            'latest_vizzys' => [
                [
                    'id' => '1', // vizzy id
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_name' => '1115. Supercast sees success in cross-platform paid podcast subscriptions',
                    'show_name' => 'Podnews podcasting news',
                ],
                [
                    'id' => '2',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_name' => '1115. Supercast sees success in cross-platform paid podcast subscriptions',
                    'show_name' => 'Podnews podcasting news',
                ]
            ],
            'top_shows' => [
                [
                    'id' => 'https://podnews.net/rss', // this will be feed url
                    'name' => 'Podnews podcasting news',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_count' => '50',
                ],
                [
                    'id' => 'https://podnews.net/rss', // this will be feed url
                    'name' => 'Podnews podcasting news',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_count' => '50',
                ]
            ],
            'your_shows' => [
                'episodes' => [
                    [
                        'id' => 'https://podnews.net/rss|https://podnews.net/update/supercast-subscription-points', // concatenation of show feed URL | episode guid
                        'image' => 'https://podnews.net/audio/podnews210903.jpeg',
                        'episode_name' => 'Supercast sees success in cross-platform paid podcast subscriptions',
                        'show_name' => 'Podnews podcasting news'

                    ],
                    [
                        'id' => 'https://podnews.net/rss|https://podnews.net/update/supercast-subscription-points', // concatenation of show feed URL | episode guid
                        'image' => 'https://podnews.net/audio/podnews210903.jpeg',
                        'episode_name' => 'Supercast sees success in cross-platform paid podcast subscriptions',
                        'show_name' => 'Podnews podcasting news'

                    ]
                ],
                'shows' => [
                    [
                        'id' => 'https://podnews.net/rss', // this will be feed url
                        'name' => 'Podnews podcasting news',
                        'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                        // 'episode_count' => '50',
                    ],
                    [
                        'id' => 'https://podnews.net/rss', // this will be feed url
                        'name' => 'Podnews podcasting news',
                        'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                        // 'episode_count' => '50',
                    ]
                ]

            ],
            'recently_played' => [
                [
                    'id' => 'https://podnews.net/rss|https://podnews.net/update/supercast-subscription-points', // concatenation of show feed URL | episode guid
                    'image' => 'https://podnews.net/audio/podnews210903.jpeg',
                    'episode_name' => 'Supercast sees success in cross-platform paid podcast subscriptions',
                    'show_name' => 'Podnews podcasting news'

                ]
            ],
            'you_may_like' => [
                [
                    'id' => 'https://podnews.net/rss', // this will be feed url
                    'name' => 'Podnews podcasting news',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_count' => '50',
                ],
                [
                    'id' => 'https://podnews.net/rss', // this will be feed url
                    'name' => 'Podnews podcasting news',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_count' => '50',
                ]
            ],
            'categories' => [
                [
                    'id' => '', // category id
                    'name' => '',
                    'image' => ''
                ],
            ],
            'trending_shows' => [
                [
                    'id' => 'https://podnews.net/rss', // this will be feed url
                    'name' => 'Podnews podcasting news',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_count' => '50',
                ],
                [
                    'id' => 'https://podnews.net/rss', // this will be feed url
                    'name' => 'Podnews podcasting news',
                    'image' => 'https://podnews.net/static/podnews-2000x2000.png',
                    'episode_count' => '50',
                ],
            ]
        ];

        $data['latest_vizzys'] = $this->getLatestVizzys();
        $data['top_shows'] = $this->getTopShows();
        $data['your_shows'] = $this->getFavouritedShows();
        $data['recently_played'] = $this->getRecentlyPlayedShows();
        $data['you_may_like'] = $this->getYouMayLike();
        $data['categories'] = $this->getCategories();
        $data['trending_shows'] = $this->getTrendingShows();

        return $this->sendResponse($data);
    }

    public function vizzyIndex(Request $request)
    {
        $hasVizzy = true;
        $data['latest_vizzys'] = $this->getLatestVizzys();
        $data['top_shows'] = $this->getTopVizzyShows();
        $data['your_shows'] = $this->getFavouritedShows($hasVizzy);
        $data['recently_played'] = $this->getRecentlyPlayedShows($hasVizzy);
        $data['categories'] = $this->getVizzyCategories();

        return $this->sendResponse($data);
    }

    /**
     *  Latest Vizzys
     */
    protected function getLatestVizzys()
    {
        $data = [];
        $vizzys = $this->vizzyPodcaster->getLatestVizzys(10);
        foreach ($vizzys as $vizzy) {
            if ($vizzy) {
                $data[] = [
                    'vizzy_id' => $vizzy->id, // vizzy id
                    'episode_id' => $vizzy->podcast->feed_url.'|'.$vizzy->episode->guid,
                    'image' => $vizzy->image,
                    'episode_name' => $vizzy->episode->title,
                    'show_name' => $vizzy->podcast->title,
                ];
            }
        }

        return $data;
    }

    /**
     *  Top Shows
     */
    protected function getTopShows()
    {
        $data = [];
        $podcasts = $this->vizzyPodcaster->getTopShows(10);
        foreach ($podcasts as $podcast) {
            $data[] = [
                'id' => $podcast->id,
                'image' => $podcast->artwork,
                'name' => $podcast->title
            ];
        }

        return $data;
    }

    /**
     *  Top Shows
     */
    protected function getTopVizzyShows()
    {
        $data = [];
        $podcasts = $this->vizzyPodcaster->getTopVizzyShows(10);
        foreach ($podcasts as $podcast) {
            $data[] = [
                'id' => $podcast->id,
                'image' => $podcast->artwork,
                'name' => $podcast->title
            ];
        }

        return $data;
    }

    /**
     * User Favourited Show
     */
    protected function getFavouritedShows($hasVizzy = false, $limit = 10)
    {
        $data = [];
        $query = Favourited::where('user_id', Auth::user()->id)->where('type','episode');
        if ($hasVizzy) {
            $query->where('has_vizzy', $hasVizzy);
        }
        $query->orderBy('created_at', 'desc')->limit($limit);
        $favourited_episode = $query->get();
        foreach($favourited_episode as $item) {
            if (!array_key_exists('episodes', $data)){
              $data['episodes'] = [];
            }
            $data['episodes'][] = [
                'id' => $item->feed_url . '|' . $item->episode_guid,
                'image' => $item->image,
                'episode_name' => $item->episode_name,
                'show_name' => $item->show_name
            ];
        }

        $query = Favourited::where('user_id', Auth::user()->id)->where('type','show');
        if ($hasVizzy) {
            $query->where('has_vizzy', $hasVizzy);
        }
        $query->orderBy('created_at', 'desc')->limit($limit);
        $favourited_show = $query->get();
        foreach($favourited_show as $item) {
            if (!array_key_exists('shows', $data)){
              $data['shows'] = [];
            }
            $data['shows'][] = [
                'id' => $item->feed_url,
                'name' => $item->show_name,
                'image' => $item->image,
            ];
        }
        return $data;
    }

    /**
     * User Played Show
     */
    protected function getRecentlyPlayedShows($hasVizzy = false)
    {
        $data = [];
        $query = Played::where('user_id', Auth::user()->id);
        if ($hasVizzy){
            $query->where('has_vizzy', 1);
        }
        $query->orderBy('updated_at','desc')->limit(10);
        $played = $query->get();
        foreach($played as $item) {
            $data[] = [
                'id' => $item->feed_url . '|' . $item->episode_guid,
                'image' => $item->image,
                'episode_name' => $item->episode_name,
                'show_name' => $item->show_name
            ];
        }
        return $data;
    }


    /**
     * Recently shows filtered by user category
     */
    protected function getYouMayLike()
    {
        $data = [];
        $podcast_categorie = Auth::user()->podcast_mapped_categories();

        $result = $this->podcastIndex->recentPodcasts($podcast_categorie);
        foreach ($result as $row) {
            $data [] = [
                'id' => $row->url,
                'name' => $row->title,
                'image' => $row->image,
                // 'episode_count' => '50',
            ];
        }

        return $data;
    }

    /**
     * Category list
     */
    protected function getCategories()
    {
        return PodcastCategoryResource::collection(PodcastCategory::orderBy('name')->get());

    }

    /**
     * Vizzy Category list
     */
    protected function getVizzyCategories()
    {
        $vizzy_cats = [];
        $cats = $this->vizzyPodcaster->getVizzyCategories();
        foreach ($cats as $cat) {
            $cs = explode(' & ', html_entity_decode($cat->category));
            foreach ($cs as $c) {
                $vizzy_cats[] = $c;
            }
        }

        $podcastCategory = PodcastCategory::select('podcast_categories.*')->leftJoin(
            'podcast_category_mappings', 'podcast_category_id', '=', 'podcast_categories.id')
            ->orWhereIn('podcast_category_mappings.name', $vizzy_cats)
            ->orWhereIn('podcast_categories.name', $vizzy_cats)
            ->groupBy('podcast_categories.id')->orderBy('name')->get();

        return PodcastCategoryResource::collection($podcastCategory);
    }

    /**
     * Trending shows filtered by user category
     */
    protected function getTrendingShows()
    {
        $data = [];
        $podcast_categorie = Auth::user()->podcast_mapped_categories();

        $result = $this->podcastIndex->trendingPodcasts($podcast_categorie);
        foreach ($result as $row) {
            $data [] = [
                'id' => $row->url,
                'name' => $row->title,
                'image' => $row->image,
                // 'episode_count' => '50',
            ];
        }

        return $data;
    }


    /**
     * Latest Vizzys - see all
     */
    protected function allLatestVizzys(Request $request)
    {
        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        $podcast_categories = null;
        $cid = $request->query('category');
        if ($cid) {
            $category = PodcastCategory::where('id', $cid)->first();
            $podcast_categories = implode('|',$category->mapped_categories());
        }

        $data = [];
        $vizzys = $this->vizzyPodcaster->getLatestVizzys($limit, $offset, $podcast_categories);
        foreach ($vizzys as $vizzy) {
            if ($vizzy) {
                $data[] = [
                    'vizzy_id' => $vizzy->id, // vizzy id
                    'episode_id' => $vizzy->podcast->feed_url.'|'.$vizzy->episode->guid,
                    'image' => $vizzy->image,
                    // 'episode_name' => $vizzy->episode->title,
                    // 'show_name' => $vizzy->podcast->title,
                    'podcast' => $vizzy->podcast,
                    'episode' => $vizzy->episode,
                ];
            }
        }

        return $this->sendResponse($data);
    }

    /**
     * Top Shows - see all
     */
    protected function allTopShows(Request $request)
    {
        $limit = 25;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        if ($request->has('has_vizzy')){
            $data = $this->vizzyPodcaster->getTopVizzyShows($limit, $offset);
        } else {
            $data = $this->vizzyPodcaster->getTopShows($limit, $offset);
        }

        return $this->sendResponse($data);
    }

    /**
     * Favourited Shows and Episodes - see all
     */
    protected function allFavourited(Request $request)
    {
        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        $data = [];
        $query = Favourited::where('user_id', Auth::user()->id)->where('type','episode');
        if ($request->has('has_vizzy')){
            $query->where('has_vizzy', $request->query('has_vizzy'));
        }
        $query->orderBy('created_at','desc')->offset($offset)->limit($limit);
        $favourited_episode = $query->get();
        foreach($favourited_episode as $item) {
            if (!array_key_exists('episodes', $data)){
              $data['episodes'] = [];
            }
            $data['episodes'][] = [
                'id' => $item->feed_url . '|' . $item->episode_guid,
                'image' => $item->image,
                'episode_name' => $item->episode_name,
                'show_name' => $item->show_name
            ];
        }

        $query = Favourited::where('user_id', Auth::user()->id)->where('type','show');
        if ($request->has('has_vizzy')){
            $query->where('has_vizzy', $request->query('has_vizzy'));
        }
        $query->orderBy('created_at', 'desc')->offset($offset)->limit($limit);
        $favourited_show = $query->get();
        foreach($favourited_show as $item) {
            if (!array_key_exists('shows', $data)){
              $data['shows'] = [];
            }
            $data['shows'][] = [
                'id' => $item->feed_url,
                'name' => $item->show_name,
                'image' => $item->image,
            ];
        }

        return $this->sendResponse($data);
    }

    /**
     * Favourited Shows - see all
     */
    protected function allFavouritedShows(Request $request)
    {
        // filter by category
        $filter_cats = [];
        $cid = $request->query('category');
        if ($cid) {
            $category = PodcastCategory::where('id', $cid)->first();
            $filter_cats = $category->mapped_categories();
        }

        // load all favourited show
        $data = [];
        $query = Favourited::where('user_id', Auth::user()->id)->where('type','show');
        if ($request->has('has_vizzy')){
            $query->where('has_vizzy', $request->query('has_vizzy'));
        }
        $favourited_show = $query->get();
        foreach($favourited_show as $item) {
            $content = $this->url_get_contents($item->feed_url);
            $parser = new Parser();
            $parser->setContent($content);
            $rss = $parser->run();

            $latest = $rss->getEpisodes()->mostRecent()->getPublishedDate()->getTimestamp();

            $favourited_cats = [];
            $cats = array_map(function($n) { return html_entity_decode($n->getName()); }, $rss->getCategories());
            foreach ($cats as $cat) {
                $cs = explode(' & ', html_entity_decode($cat));
                foreach ($cs as $c) {
                    $favourited_cats[] = $c;
                }
            }

            if ($filter_cats) {
                if (!array_intersect($filter_cats,$favourited_cats)) {
                    continue;
                }
            }

            if (!array_key_exists('shows', $data)){
              $data['shows'] = [];
            }

            $data['shows'][] = [
                'id' => $item->feed_url,
                'name' => $item->show_name,
                'image' => $item->image,
                'has_vizzy' => $item->has_vizzy,
                'latest_ep' => $latest,
            ];
        }

        // sort by
        if (isset($data['shows'])) {
          if ($request->query('sort') == 'a-z') {
              usort($data['shows'], fn($a,$b) => strcmp($a['name'],$b['name']));
          }
          elseif ($request->query('sort') == 'z-a') {
              usort($data['shows'], fn($a,$b) => strcmp($b['name'],$a['name']));
          }
          else {
              usort($data['shows'], fn($a,$b) => $b['latest_ep'] - $a['latest_ep']);
          }
        }

        // pagination
        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);
        if (isset($data['shows'])) {
            $data['shows'] = array_slice($data['shows'], $offset, $limit);
        }

        $podcastCategory = PodcastCategory::select('podcast_categories.*')->leftJoin(
            'podcast_category_mappings', 'podcast_category_id', '=', 'podcast_categories.id')
            ->orWhereIn('podcast_category_mappings.name', $favourited_cats)
            ->orWhereIn('podcast_categories.name', $favourited_cats)
            ->groupBy('podcast_categories.id')->orderBy('name')->get();

        $data['topics'] = PodcastCategoryResource::collection($podcastCategory);

        return $this->sendResponse($data);
    }

    /**
     * Favourited Episodes - see all
     */
    protected function allFavouritedEpisodes(Request $request)
    {
        $data = [];
        $query = Favourited::where('user_id', Auth::user()->id)->where('type','episode');
        if ($request->query('filter') == 'vizzy'){
            $query->where('has_vizzy', 1);
        } elseif ($request->query('filter') == 'novizzy'){
            $query->where('has_vizzy', 0);
        }
        $favourited_episode = $query->get();
        foreach($favourited_episode as $item) {

            // Check episode played
            $played = Played::where('user_id', $request->user()->id)
                ->where('feed_url', $item->feed_url)
                ->where('episode_guid', $item->episode_guid)
                ->first();

            if ($request->query('filter') == 'played' && !$played) {
                continue;
            }
            elseif ($request->query('filter') == 'unplayed' && $played) {
                continue;
            }

            $content = $this->url_get_contents($item->feed_url);
            $parser = new Parser();
            $parser->setContent($content);
            $rss = $parser->run();

            if ($rss->getEpisodes()->findByGuid($item->episode_guid)) {
                $pub_date = $rss->getEpisodes()->findByGuid($item->episode_guid)->getPublishedDate()->getTimestamp();
            } else {
                $pub_date = 0;
            }

            $data[] = [
                'id' => $item->feed_url . '|' . $item->episode_guid,
                'image' => $item->image,
                'episode_name' => $item->episode_name,
                'show_name' => $item->show_name,
                'has_vizzy' => $item->has_vizzy,
                'pub_date' => $pub_date
            ];
        }

        if ($request->query('sort') == 'newest') {
            usort($data, fn($a,$b) => $a['pub_date'] - $b['pub_date']);
        }

        // pagination
        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);
        $data = array_slice($data, $offset, $limit);

        return $this->sendResponse($data);
    }

    /**
     * User Played Show - seel all
     */
    protected function allRecentlyPlayedShows(Request $request)
    {
        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        $data = [];
        $query = Played::where('user_id', Auth::user()->id);
        if ($request->has('has_vizzy')){
            $query->where('has_vizzy', $request->query('has_vizzy'));
        }
        $query->orderBy('updated_at','desc')->offset($offset)->limit($limit);
        $played = $query->get();
        foreach($played as $item) {
            $data[] = [
                'id' => $item->feed_url . '|' . $item->episode_guid,
                'image' => $item->image,
                'episode_name' => $item->episode_name,
                'show_name' => $item->show_name
            ];
        }
        return $this->sendResponse($data);
    }

    /**
     * All Category -- see all
     */
    protected function allCategories(Request $request)
    {
        if ($request->query('has_vizzy')) {
            $data = $this->getVizzyCategories();
        }
        else {
            $data = $this->getCategories();
        }

        return $this->sendResponse($data);
    }

    /**
     * Recently shows filtered by category -- see all
     */
    protected function allLatestShows(Request $request)
    {
        $data = [];
        $podcast_categories = Auth::user()->podcast_mapped_categories();

        $cid = $request->query('category');
        if ($cid) {
            $category = PodcastCategory::where('id', $cid)->first();
            $podcast_categories = $category->mapped_categories();
        }

        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        if ($request->query('has_vizzy')) {
            $podcast_categories = implode('|',$podcast_categories);
            $result = $this->vizzyPodcaster->getLatestShowsWithVizzys($limit, $offset, $podcast_categories);
            foreach ($result as $show) {
                $data[] = [
                    'id' => $show->url,
                    'name' => $show->title,
                    'image' => $show->image,
                ];
            }
        } else {
            $result = $this->podcastIndex->recentPodcasts($podcast_categories,500);
            foreach ($result as $idx => $row) {
                if ($idx >= $offset + $limit) {
                    break;
                }
                if ($idx >= $offset) {
                    $data [] = [
                        'id' => $row->url,
                        'name' => $row->title,
                        'image' => $row->image,
                    ];
                }
            }
        }

        return $this->sendResponse($data);
    }

    /**
     * Trending shows filtered by user category -- see all
     */
    protected function allTrendingShows(Request $request)
    {
        $data = [];
        $podcast_categories = Auth::user()->podcast_mapped_categories();

        $limit = 20;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        $result = $this->podcastIndex->trendingPodcasts($podcast_categories,500);
        foreach ($result as $idx => $row) {
            if ($idx >= $offset + $limit) {
                break;
            }
            if ($idx >= $offset) {
                $data [] = [
                    'id' => $row->url,
                    'name' => $row->title,
                    'image' => $row->image,
                ];
            }
        }

        return $this->sendResponse($data);
    }
}