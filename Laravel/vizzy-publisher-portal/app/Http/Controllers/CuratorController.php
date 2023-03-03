<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Exception\RequestException;
use App\Models\Podcast;
use App\Models\Vizzy;
use App\Models\VizzyCard;
use App\Models\InteractionInfo;
use App\Models\InteractionSocialGroup;
use App\Models\InteractionSocialGroupLink;
use App\Models\InteractionProduct;
use App\Models\InteractionWeb;
use App\Models\InteractionWebLink;
use App\Traits\EscapeFileUrlTrait;
use App\Mail\VizzyPublishRequestMail;
use App\WebServices\VizzyApi\VizzyApi;
use App\Events\VizzyCreated;

class CuratorController extends Controller
{
    use EscapeFileUrlTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display curator tool
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Request $request, Podcast $podcast) {

        if ($podcast->user_id != Auth::user()->id && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $guid = urldecode($request->input('guid'));
        $rss = $podcast->rss();
        $episode = $rss->getEpisodes()->findByGuid($guid);
        $audio_url = $episode->getMedia()->getUri();

        $vizzy = $podcast->getVizzyByGuid($guid);
        if ($vizzy) {
            $cards = $vizzy->content;
            $vizzy_image = $vizzy->image;
            $vizzy_status = $vizzy->status_name;
            $button_status = $vizzy->button_status;
            if ($vizzy->audio_url) {
              $audio_url = $vizzy->audio_url;
            }
        } else {
            $cards = '';
            $vizzy_image = '';
            $vizzy_status = 'Draft';
            $button_status = 'Publish';
        }

        $s3_url = Storage::disk('s3')->url('icons/');

        return view('curator.show', compact(
            'podcast','rss','episode','cards','vizzy','vizzy_image','vizzy_status','button_status','s3_url','audio_url'));
    }


    /**
     * Display curator tool
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function store(Request $request, Podcast $podcast) {

        if ($podcast->user_id != Auth::user()->id && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $this->validate($request, [
            'vizzy_image' => 'required'
        ]);

        $guid = urldecode($request->input('guid'));
        $cards = $request->input('cards');
        $vizzy_image = str_replace(' ', '+', $request->input('vizzy_image'));

        try {
            DB::beginTransaction();

            $episode = $podcast->rss()->getEpisodes()->findByGuid($guid);
            $vizzy = $podcast->getVizzyByGuid($guid);
            if (!$vizzy) {
                $vizzy = new Vizzy();
                $vizzy->podcast_id = $podcast->id;
                $vizzy->episode_guid = $guid;
                $vizzy->status = Vizzy::STATUS_DRAFT;
            }

            $vizzy->title = $episode->getTitle();
            $vizzy->content = $cards;

            if (!$vizzy->id) {
                $vizzy->save();
            }

            if ($vizzy_image) {
                $id = $vizzy->id;
                $path_parts = pathinfo($vizzy_image);
                $ext = $path_parts['extension'];
                // resize and crop image
                $img = \Image::make($vizzy_image);
                $img->fit(882,543,function ($constraint) {
                    $constraint->upsize();
                });
                Storage::disk('s3')->put('vizzy-tile/'.$id.'.'.$ext, $img->stream()->__toString());
                $path = Storage::disk('s3')->url('vizzy-tile/'.$id.'.'.$ext);
                $vizzy->image = $path; //$episode->getArtwork() ? $episode->getArtwork()->getUri() : null;
                $vizzy->save();
            } else {
                if ($vizzy->image) {
                    $path_parts = pathinfo($vizzy->image);
                    $ext = $path_parts['extension'];
                    Storage::disk('s3')->delete('vizzy-tile/'.$vizzy->id.'.'.$ext);
                    $vizzy->image = '';
                    $vizzy->save();
                }
            }

            // download audio_url
            if (!$vizzy->audio_url) {
                event(new VizzyCreated($vizzy));
            }

            $vizzy->cards()->delete();

            $objCards = json_decode($cards);
            foreach($objCards as $objCard) {
                $card = new VizzyCard();
                $card->vizzy_id = $vizzy->id;
                $card->title = $objCard->title;
                $card->content = $objCard->content;
                $card->image = $objCard->image;
                $card->start = $objCard->start;
                $card->end = $objCard->end;
                $card->save();

                if ($objCard->interactions->info) {
                    $info = new InteractionInfo();
                    $info->card_id = $card->id;
                    $info->title = $objCard->interactions->info->title;
                    $info->image = $objCard->interactions->info->image;
                    $info->content = $objCard->interactions->info->content;
                    $info->save();
                }

                if ($objCard->interactions->social) {
                    foreach ($objCard->interactions->social as $group) {
                        $social = new InteractionSocialGroup();
                        $social->card_id = $card->id;
                        $social->title = $group->title;
                        $social->save();
                        foreach ($group->links as $link) {
                            if ($link->type && $link->url) {
                                $social_link = new InteractionSocialGroupLink();
                                $social_link->group_id = $social->id;
                                $social_link->type = $link->type;
                                $social_link->url = $link->url;
                                $social_link->save();
                            }
                        }
                    }
                }

                if ($objCard->interactions->product) {
                    $product = new InteractionProduct();
                    $product->card_id = $card->id;
                    $product->type = $objCard->interactions->product->type;
                    $product->title = $objCard->interactions->product->title;
                    $product->image = $objCard->interactions->product->image;
                    $product->content = $objCard->interactions->product->content;
                    $product->url =  $objCard->interactions->product->url;
                    $product->save();
                }

                if ($objCard->interactions->web) {
                    foreach ($objCard->interactions->web as $group) {
                        $web = new InteractionWeb();
                        $web->card_id = $card->id;
                        $web->title = $group->title;
                        $web->image = $group->image;
                        $web->content = $group->content;
                        $web->save();
                        foreach ($group->links as $link) {
                            if ($link->type && $link->url) {
                                $web_link = new InteractionWebLink();
                                $web_link->group_id = $web->id;
                                $web_link->type = $link->type;
                                $web_link->url = $link->url;
                                $web_link->save();
                            }
                        }
                    }
                }

            }

            DB::commit();

            Cache::forget('vizzy_'.$vizzy->id);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Vizzy cannot be saved!'. $e->getMessage().$cards);
        }

        return back()->with('success', 'Vizzy updated!');
    }

    /**
     * Autosave when card is added
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function autoSave(Request $request, Podcast $podcast) {

        if ($podcast->user_id != Auth::user()->id && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $this->validate($request, [
            'guid' => 'required'
        ]);

        $guid = urldecode($request->input('guid'));
        $cards = $request->input('cards');

        try {
            DB::beginTransaction();

            $episode = $podcast->rss()->getEpisodes()->findByGuid($guid);
            $vizzy = $podcast->getVizzyByGuid($guid);
            if (!$vizzy) {
                $vizzy = new Vizzy();
                $vizzy->podcast_id = $podcast->id;
                $vizzy->episode_guid = $guid;
                $vizzy->status = Vizzy::STATUS_DRAFT;
            }

            $vizzy->title = html_entity_decode($episode->getTitle());
            $vizzy->content = $cards;
            $vizzy->save();

            // download audio_url
            if (!$vizzy->audio_url) {
                event(new VizzyCreated($vizzy));
            }

            $vizzy->cards()->delete();

            $objCards = json_decode($cards);
            foreach($objCards as $objCard) {
                $card = new VizzyCard();
                $card->vizzy_id = $vizzy->id;
                $card->title = $objCard->title;
                $card->content = $objCard->content;
                $card->image = $objCard->image;
                $card->start = $objCard->start;
                $card->end = $objCard->end;
                $card->save();

                if ($objCard->interactions->info) {
                    $info = new InteractionInfo();
                    $info->card_id = $card->id;
                    $info->title = $objCard->interactions->info->title;
                    $info->image = $objCard->interactions->info->image;
                    $info->content = $objCard->interactions->info->content;
                    $info->save();
                }

                if ($objCard->interactions->social) {
                    foreach ($objCard->interactions->social as $group) {
                        $social = new InteractionSocialGroup();
                        $social->card_id = $card->id;
                        $social->title = $group->title;
                        $social->save();
                        foreach ($group->links as $link) {
                            if ($link->type && $link->url) {
                                $social_link = new InteractionSocialGroupLink();
                                $social_link->group_id = $social->id;
                                $social_link->type = $link->type;
                                $social_link->url = $link->url;
                                $social_link->save();
                            }
                        }
                    }
                }

                if ($objCard->interactions->product) {
                    $product = new InteractionProduct();
                    $product->card_id = $card->id;
                    $product->type = $objCard->interactions->product->type;
                    $product->title = $objCard->interactions->product->title;
                    $product->image = $objCard->interactions->product->image;
                    $product->content = $objCard->interactions->product->content;
                    $product->url =  $objCard->interactions->product->url;
                    $product->save();
                }

                if ($objCard->interactions->web) {
                    foreach ($objCard->interactions->web as $group) {
                        $web = new InteractionWeb();
                        $web->card_id = $card->id;
                        $web->title = $group->title;
                        $web->image = $group->image;
                        $web->content = $group->content;
                        $web->save();
                        foreach ($group->links as $link) {
                            if ($link->type && $link->url) {
                                $web_link = new InteractionWebLink();
                                $web_link->group_id = $web->id;
                                $web_link->type = $link->type;
                                $web_link->url = $link->url;
                                $web_link->save();
                            }
                        }
                    }
                }

            }

            DB::commit();

            Cache::forget('vizzy_'.$vizzy->id);

        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(['success'=>false, 'message'=>$e->getMessage()]);
        }

        return json_encode(['success'=>true]);
    }

    /**
     * Publish Vizzy
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function publish(Request $request, Podcast $podcast) {

        if ($podcast->user_id != Auth::user()->id && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $this->validate($request, [
            'guid' => 'required'
        ]);

        $guid = urldecode($request->input('guid'));
        $vizzy = $podcast->getVizzyByGuid($guid);
        if ($vizzy) {

            $vizzy->status = Vizzy::STATUS_PENDING;
            $vizzy->save();

            // notify admin of podcast approval
            if (config('vizzy.notify_email')) {
                $emails = explode(',', config('vizzy.notify_email'));
                Mail::to($emails)->send(new VizzyPublishRequestMail($vizzy));
            }

            return back()->with('success', 'Vizzy has been submitted for approval');

            // $vizzy->status = Vizzy::STATUS_PUBLISHED;
            // $vizzy->published_at = \Carbon\Carbon::now();
            // $vizzy->save();

            // return back()->with('success', 'Vizzy has been published');
        }

        return back()->with('error', 'Vizzy cannot be found');
    }

    /**
     * Un-Publish Vizzy
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function unpublish(Request $request, Podcast $podcast, VizzyApi $vizzyApi) {

        if ($podcast->user_id != Auth::user()->id && !$request->user()->hasRole('admin')) {
            abort(403);
        }

        $this->validate($request, [
            'guid' => 'required'
        ]);

        $guid = urldecode($request->input('guid'));
        $vizzy = $podcast->getVizzyByGuid($guid);
        if ($vizzy) {
            if ($vizzy->published_at) {
                $vizzy->status = Vizzy::STATUS_UNPUBLISHED;
                $msg = 'Vizzy has been unpublished';
            } else {
                $vizzy->status = Vizzy::STATUS_DRAFT;
                $msg = 'Vizzy has been withdrawn from approval list';
            }
            $vizzy->save();

            // notify app of unpublished of vizzy
            $vizzyApi->unpublish($vizzy->episode_guid);

            return back()->with('success', $msg);
        }

        return back()->with('error', 'Vizzy cannot be found');
    }

    /**
     * Delete Vizzy
     */
    public function delete(Request $request, Vizzy $vizzy) {

        if ($vizzy->image) {
          $path_parts = pathinfo($vizzy->image);
          $ext = $path_parts['extension'];
          Storage::disk('s3')->delete('vizzy-tile/'.$vizzy->id.'.'.$ext);
        }

        if ($vizzy->audio_url) {
          $path_parts = pathinfo($vizzy->audio_url);
          $filename = $path_parts['filename'];
          $ext = $path_parts['extension'];
          Storage::disk('s3')->delete('vizzy-audio/vizzy'.$id.'-'.$filename.'.'.$ext);
        }

        $podcast = $vizzy->podcast;
        $vizzy->delete();

        return redirect(route('episodes', $podcast->id))->with('success', 'Vizzy deleted!');;
    }
}
