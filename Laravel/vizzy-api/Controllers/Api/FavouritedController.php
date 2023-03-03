<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favourited;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;

class FavouritedController extends Controller
{
    protected $vizzyPodcaster;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(VizzyPodcaster $vizzyPodcaster)
    {
        $this->vizzyPodcaster = $vizzyPodcaster;
    }

    public function addFavourite(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'id' => 'required',
            'image' => 'required',
            'show_name' => 'required',
            'episode_name' => 'required_if:type,episode',
        ]);

        if ($request->input('type') == 'episode') {
            if (strpos($request->input('id'),'|') > -1) {
                [$feed_url, $episode_guid] = explode('|',$request->input('id'));
            } else {
                return $this->sendError('Invalid id',200);
            }
        } else {
            $feed_url = $request->input('id');
            $episode_guid = '';
        }

        try {
            $favourited = new Favourited();
            $favourited->user_id = $request->user()->id;
            $favourited->type = $request->input('type');
            $favourited->image = $request->input('image');
            $favourited->show_name = $request->input('show_name');
            $favourited->episode_name = $request->input('episode_name');
            $favourited->feed_url = $feed_url;
            if ($request->input('type') == 'show' && !$request->input('has_vizzy')) {
                $favourited->has_vizzy = $this->vizzyPodcaster->hasVizzy($request->input('id')) ? true : false;
            } else {
                $favourited->has_vizzy = $request->input('has_vizzy') ? $request->input('has_vizzy') : false;
            }
            $favourited->episode_guid = $episode_guid;
            $favourited->save();
        }
        catch (\Exception $e) {
            return $this->sendError('Error saving ' . $request->input('type'), 200);
        }

        return $this->sendResponse('','Saved successfully');
    }

    public function removeFavourite(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'id' => 'required',
        ]);

        if ($request->input('type') == 'episode') {
            if (strpos($request->input('id'), '|') > -1) {
                [$feed_url, $episode_guid] = explode('|',$request->input('id'));
            } else {
                return $this->sendError('Invalid id',200);
            }
        } else {
            $feed_url = $request->input('id');
            $episode_guid = '';
        }

        $favourited = Favourited::where('user_id', $request->user()->id)
            ->where('type', $request->input('type'))
            ->where('feed_url', $feed_url)
            ->where('episode_guid', $episode_guid)
            ->delete();

        return $this->sendResponse('','Deleted successfully');

    }
}
