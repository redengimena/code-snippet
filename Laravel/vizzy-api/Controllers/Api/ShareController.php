<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Lukaswhite\PodcastFeedParser\Parser;
use App\WebServices\VizzyPodcaster\VizzyPodcaster;
use App\Models\Share;
use App\Models\Snippet;



class ShareController extends Controller
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

    /**
     * Share snippet
     */
    protected function share(Request $request)
    {
        $this->validate($request, [
            'feed_url' => 'required',
            // 'episode_guid' => 'required',
        ]);

        $feed_url = $request->input('feed_url');
        $episode_guid = $request->input('episode_guid');

        $share = Share::where('feed_url', $feed_url)
            ->where('episode_guid', $episode_guid)
            ->first();

        if (!$share) {
            $share = new Share();
            $share->feed_url = $feed_url;
            $share->episode_guid = $episode_guid;
            $share->save();
        }

        $share_url = config('webservices.vizzyshare.base_url');
        $data = [
          'url' => $share_url . $share->slug()
        ];

        return $this->sendResponse($data);
    }

    /**
     * Get Share by slug
     */
    protected function shareSlug(Request $request)
    {
        $this->validate($request, [
            'slug' => 'required',
        ]);

        $slug = $request->input('slug');

        $share = Share::findBySlug($slug);
        if ($share) {
            $data = [
              'feed_url' => $share->feed_url,
              'episode_guid' => $share->episode_guid,
            ];

            return $this->sendResponse($data);
        }

        return $this->sendError();
    }

    /**
     * Save snippet
     */
    protected function snippet(Request $request)
    {
        $this->validate($request, [
            'feed_url' => 'required',
            'episode_guid' => 'required',
            'title' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        $feed_url = $request->input('feed_url');
        $episode_guid = $request->input('episode_guid');
        $title = $request->input('title');
        $content = $request->input('content');
        $start = $request->input('start');
        $end = $request->input('end');

        $snippet = new Snippet();
        $snippet->user_id = $request->user()->id;
        $snippet->feed_url = $feed_url;
        $snippet->episode_guid = $episode_guid;
        $snippet->title = $title;
        $snippet->content = $content;
        $snippet->start = $start;
        $snippet->end = $end;
        $snippet->save();

        return $this->sendResponse($snippet);
    }

    /**
     * Save snippet
     */
    protected function saveSnippet(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'feed_url' => 'required',
            'episode_guid' => 'required',
            'show_name' => 'required',
            'episode_name' => 'required',
            'image' => 'required',
            'pub_date' => 'required',
            'title' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        $type = $request->input('type');
        $feed_url = $request->input('feed_url');
        $episode_guid = $request->input('episode_guid');
        $show_name = $request->input('show_name');
        $episode_name = $request->input('episode_name');
        $image = $request->input('image');
        $title = $request->input('title');
        $content = $request->input('content');
        $start = $request->input('start');
        $end = $request->input('end');
        $pub_date = strtotime($request->input('pub_date'));

        $snippet = new Snippet();
        $snippet->user_id = $request->user()->id;
        $snippet->type = $type;
        $snippet->feed_url = $feed_url;
        $snippet->episode_guid = $episode_guid;
        $snippet->show_name = $show_name;
        $snippet->episode_name = $episode_name;
        $snippet->image = $image;
        $snippet->pub_date = date('Y-m-d', $pub_date);
        $snippet->title = $title;
        $snippet->content = $content;
        $snippet->start = $start;
        $snippet->end = $end;
        $snippet->save();

        return $this->sendResponse($snippet);
    }

    /**
     * Load snippet
     */
    protected function loadSnippet(Request $request)
    {
        $limit = 10;
        $page = $request->query('page') && ctype_digit($request->query('page')) ? $request->query('page') : 1;
        $offset = $limit * ($page-1);

        $query = Snippet::where('user_id', $request->user()->id);

        if ($request->query('filter') == 'snippet') {
            $query->where('type', 'snippet');
        }
        else if ($request->query('filter') == 'card') {
            $query->where('type', 'card');
        }

        if ($request->query('sort') == 'saved-a-z') {
            $query->orderBy('created_at');
        }
        else if ($request->query('sort') == 'saved-z-a') {
            $query->orderBy('created_at','desc');
        }
        else if ($request->query('sort') == 'pub-a-z') {
            $query->orderBy('pub_date');
        }
        else if ($request->query('sort') == 'pub-z-a') {
            $query->orderBy('pub_date','desc');
        }

        $data = [];
        $query->offset($offset)->limit($limit);
        $snippets = $query->get();
        foreach($snippets as $snippet) {
            $item = $snippet;
            $seconds = $snippet->end - $snippet->start;
            $duration = '';
            if ($seconds > 60) {
                $duration .= gmdate('i\m', $seconds);
            }
            $duration .= gmdate('s\s', $seconds);
            $item['duration'] = $duration;
            $data[] = $item;
        }
        return $this->sendResponse($data);
    }

    /**
     * Edit snippet
     */
    protected function updateSnippet(Request $request, Snippet $snippet)
    {
        if (!$snippet) {
            return $this->sendError('Invalid Id', 200);
        }

        if ($snippet->user_id != $request->user()->id) {
            return $this->sendError('Snippet not found', 200);
        }

        if ($request->input('title')) {
            $snippet->title = $request->input('title');
        }
        if ($request->input('content')) {
            $snippet->content = $request->input('content');
        }
        $snippet->save();

        return $this->sendResponse($snippet);

    }

    /**
     * Delete snippet
     */
    protected function deleteSnippet(Request $request)
    {
        if (!$request->query('id')) {
            return $this->sendError('Invalid Id', 200);
        }

        $snippet = Snippet::where('user_id', $request->user()->id)->where('id', $request->query('id'))->first();
        if ($snippet) {
            $snippet->delete();
            return $this->sendResponse(null, 'Snippet has been deleted.');
        } else {
            return $this->sendError('Snippet not found', 200);
        }
    }
}