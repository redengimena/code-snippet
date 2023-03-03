<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lukaswhite\PodcastFeedParser\Parser;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;
use App\WebServices\PodcastIndex\PodcastIndex;
use App\Models\SearchHistory;


class SearchController extends Controller
{
    /**
     * search
     */
    protected function search(Request $request, PodcastIndex $podcastIndex)
    {
        $keyword = $request->input('keyword');
        $scope = $request->input('scope');

        if ($scope == 'shows') {
            $data = [
                'shows' => $podcastIndex->searchPodcastsByTerm($keyword)
            ];
        }
        elseif ($scope == 'episodes') {
            $data = [
                'episodes' => $podcastIndex->searchEpisodesByPerson($keyword)
            ];
        }
        else {
            $data = [
              'shows' => array_slice($podcastIndex->searchPodcastsByTerm($keyword),0,20),
              'episodes' => array_slice($podcastIndex->searchEpisodesByPerson($keyword),0,20)
            ];
        }

        $history = new SearchHistory();
        $history->user_id = $request->user()->id;
        $history->keyword = $keyword;
        $history->scope = $scope;
        $history->save();

        return $this->sendResponse($data);
    }


    /**
     * return popular search term
     */
    public function getPopularSearchTerms() {
        $terms = [];
        $date = \Carbon\Carbon::today()->subDays(7);

        $result = SearchHistory::select('keyword', DB::raw('count(id) as num'))
            ->where('created_at','>=',$date)
            ->groupBy('keyword')
            ->havingRaw("COUNT(num) > 1")
            ->orderBy('num', 'desc')
            ->limit(10)
            ->get();
        foreach ($result as $row) {
          $terms[] = $row->keyword;
        }

        return $this->sendResponse($terms);
    }

}