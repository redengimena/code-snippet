<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Played;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;

class PlayedController extends Controller
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

    public function addPlayed(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'image' => 'required',
            'show_name' => 'required',
            'episode_name' => 'required',
        ]);

        if (strpos($request->input('id'),'|') > -1) {
            [$feed_url, $episode_guid] = explode('|',$request->input('id'));
        } else {
            return $this->sendError('Invalid id', 200);
        }

        $played = Played::where('user_id', $request->user()->id)
            ->where('feed_url', $feed_url)
            ->where('episode_guid', $episode_guid)
            ->first();
        if (!$played) {
            $played = new Played();
            $played->user_id = $request->user()->id;
            $played->feed_url = $feed_url;
            $played->episode_guid = $episode_guid;
        }

        try {
            $played->image = $request->input('image');
            $played->show_name = $request->input('show_name');
            $played->episode_name = $request->input('episode_name');
            $played->elapsed = $request->input('elapsed');
            $played->has_vizzy = $request->input('has_vizzy') ? $request->input('has_vizzy') : false;
            $played->save();
        }
        catch (\Exception $e) {
            return $this->sendError('Error saving episode' . $e->getMessage(), 200);
        }

        return $this->sendResponse('','Saved successfully');
    }
}
