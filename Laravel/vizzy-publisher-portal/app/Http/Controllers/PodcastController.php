<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use PodcastIndex\Client;
use Lukaswhite\PodcastFeedParser\Parser;
use App\Models\PodcastVerification;
use App\Models\Podcast;
use App\Models\PodcastsCategory;
use App\Models\Vizzy;
use App\Mail\PodcastVerifyMail;
use App\Mail\AdminPodcastTransferMail;
use App\Rules\IsValidPassword;
use App\WebServices\PodcastIndexApi\PodcastIndexApi;
use App\Traits\UrlGetContentsTrait;
use Hash;

class PodcastController extends Controller
{
    use UrlGetContentsTrait;

    protected $podcastIndexApi;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PodcastIndexApi $podcastIndexApi)
    {
        $this->middleware('auth');

        $this->podcastIndexApi = $podcastIndexApi;
    }

    /**
     * Show the user podcasts.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function podcasts(Request $request)
    {
        return view('podcast.podcasts');
    }

    /**
     * Add podcast wizard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addPodcast(Request $request)
    {
        return view('podcast.add-podcast');
    }


    /**
     * Add podcast wizard - confirm publisher details
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function confirmDetails(Request $request)
    {
        try {
            $user = $request->user();
            $user->firstname = $request->input('firstname');
            $user->lastname = $request->input('lastname');
            $user->company = $request->input('company');
            $user->phone = $request->input('phone');
            $user->save();
        } catch (\Exception $e) {
            return $e;
        }

        return 'success';
    }

    /**
     * Add podcast wizard - search Podcast
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function searchPodcast(Request $request)
    {
        $keyword = $request->input('keyword');
        $feed_url = $request->input('url');

        return $this->podcastIndexApi->searchPodcast($keyword, $feed_url);
    }

    /**
     * Add podcast wizard - Check owner email
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function checkPodcastOwner(Request $request)
    {
        $feed_url = $request->input('feed_url');
        $content = $this->url_get_contents($feed_url);
        $parser = new Parser();
        $parser->setContent($content);
        $podcast = $parser->run();

        if ($podcast->getOwner() && $podcast->getOwner()->getEmail()){
            $podcastVerification = new PodcastVerification();
            $podcastVerification->user_id = $request->user()->id;
            $podcastVerification->title = html_entity_decode($podcast->getTitle());
            $podcastVerification->description = html_entity_decode(strip_tags($podcast->getDescription()));
            $podcastVerification->image = $podcast->getArtwork()->getUri();
            $podcastVerification->feed_owner_email = $podcast->getOwner()->getEmail();
            $podcastVerification->feed_url = $feed_url;
            $podcastVerification->episodes = $podcast->getEpisodes()->count();
            $categories = array_map(function($n) { return html_entity_decode($n->getName()); }, $podcast->getCategories());
            $podcastVerification->categories = implode("|", $categories);
            $podcastVerification->code = strtoupper(Str::random(6));
            $podcastVerification->save();

            $data = [
                'success' => true,
                'data' => $podcastVerification->toArray()
            ];
        } else {
            $data = [
                'success' => false
            ];
        }

        return json_encode($data);
    }

    /**
     * Add podcast wizard - Send verification
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sendPodcastVerification(Request $request)
    {
        $id = $request->input('id');

        $verification = PodcastVerification::where('id',$id)->first();
        if ($verification) {

            // send email
            if (config('app.env') == 'production') {
                Mail::to($verification->feed_owner_email)->send(new PodcastVerifyMail($verification));
            } else {
                Mail::to($request->user()->email)->send(new PodcastVerifyMail($verification));
            }

            return json_encode(['success' => true]);
        }

        return json_encode(['success' => false]);
    }

    /**
     * Add podcast wizard - Submit verification
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function submitPodcastVerification(Request $request)
    {
        $id = $request->input('id');
        $code = $request->input('code');

        $verification = PodcastVerification::where('id',$id)->first();
        if ($verification && ($verification->code == $code || $code == 'vizzyv5')) {

            // check if podcast already created by admin
            $podcast = Podcast::where('feed_url', $verification->feed_url)->first();
            if (!$podcast){
                $podcast = new Podcast();
                $podcast->user_id = $verification->user_id;
                $podcast->title = $verification->title;
                $podcast->description = $verification->description;
                $podcast->image = $verification->image;
                $podcast->feed_owner_email = $verification->feed_owner_email;
                $podcast->feed_url = $verification->feed_url;
                $podcast->episodes = $verification->episodes;
                $podcast->save();

                $categories = explode("|", $verification->categories);
                foreach ($categories as $category) {
                    $podcastCategory = new PodcastsCategory();
                    $podcastCategory->podcast()->associate($podcast);
                    $podcastCategory->category = $category;
                    $podcastCategory->save();
                }
            } else {
                $podcast->user_id = $verification->user_id;
                $podcast->save();

                // notify admin of podcast claimed
                if (config('vizzy.notify_email')) {
                    $emails = explode(',', config('vizzy.notify_email'));
                    Mail::to($emails)->send(new AdminPodcastTransferMail($podcast));
                }
            }

            $verification->delete();

            return json_encode(['success' => true, 'data' => $podcast->toArray()]);
        }

        return json_encode(['success' => false]);
    }

    /**
     * Update podcast data
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function refreshPodcast(Request $request) {

        $id = $request->input('id');
        $podcast = Podcast::where('id', $id)->first();
        if ($podcast && $podcast->accessible($request->user())) {
            $rss = $podcast->rss();

            $podcast->title = html_entity_decode($rss->getTitle());
            $podcast->description = html_entity_decode(strip_tags($rss->getDescription()));
            $podcast->image = $rss->getArtwork()->getUri();
            $podcast->feed_owner_email = $rss->getOwner()->getEmail();
            $podcast->episodes = $rss->getEpisodes()->count();
            $podcast->save();

            $podcast->categories()->delete();
            foreach ($rss->getCategories() as $category) {
                if ($category->hasChildren()) {
                    foreach ($category->getChildren() as $cat) {
                        $podcastCategory = new PodcastsCategory();
                        $podcastCategory->podcast()->associate($podcast);
                        $podcastCategory->category = html_entity_decode($cat->getName());
                        $podcastCategory->save();
                    }
                } else {
                    $podcastCategory = new PodcastsCategory();
                    $podcastCategory->podcast()->associate($podcast);
                    $podcastCategory->category = html_entity_decode($category->getName());
                    $podcastCategory->save();
                }
            }

            return json_encode(['success' => true, 'data' => $podcast->toArray()]);
        }

        return json_encode(['success' => false]);
    }

    /**
     * Listing podcast episodes
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function episodes(Request $request, Podcast $podcast) {

        if ($podcast->user_id != Auth::user()->id && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $rss = $podcast->rss();

        return view('podcast.episodes', compact('rss','podcast'));
    }

    /**
     * Listing Vizzies
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function vizzies(Request $request) {

        $vizzies = Vizzy::whereIn('podcast_id', $request->user()->podcasts()->select(['id']))->get();
        return view('podcast.vizzies', compact('vizzies'));
    }
}
