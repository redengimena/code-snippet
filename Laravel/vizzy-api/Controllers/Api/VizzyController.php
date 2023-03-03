<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lukaswhite\PodcastFeedParser\Parser;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;
use App\Models\Played;
use App\Models\Favourited;


class VizzyController extends Controller
{
    /**
     * when vizzy is published in portal
     */
    protected function publish(Request $request)
    {
        $id = $request->input('id');
        $data = [];

        $favourited = Favourited::where('episode_guid', $id)->get();
        foreach ($favourited as $fav) {
            $fav->has_vizzy = 1;
            $fav->timestamps = false;
            $fav->save();
        }

        $played = Played::where('episode_guid', $id)->get();
        foreach ($played as $p) {
            $p->has_vizzy = 1;
            $p->timestamps = false;
            $p->save();
        }

        return $this->sendResponse();
    }

    /**
     * when vizzy is unpublished in portal
     */
    protected function unpublish(Request $request)
    {
        $id = $request->input('id');
        $data = [];

        $favourited = Favourited::where('episode_guid', $id)->get();
        foreach ($favourited as $fav) {
            $fav->has_vizzy = 0;
            $fav->timestamps = false;
            $fav->save();
        }

        $played = Played::where('episode_guid', $id)->get();
        foreach ($played as $p) {
            $p->has_vizzy = 0;
            $p->timestamps = false;
            $p->save();
        }

        return $this->sendResponse();
    }

}