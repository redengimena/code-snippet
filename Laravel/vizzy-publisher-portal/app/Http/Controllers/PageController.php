<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use App\Rules\IsValidPassword;
use App\Models\Podcast;
use App\Models\Vizzy;
use Hash;

class PageController extends Controller
{
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
     * Accepting Guidelines.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function guidelines(Request $request)
    {
        if ($request->method() == 'POST') {
            $request->user()->terms_accepted_at = Carbon::now();
            $request->user()->save();

            return redirect(route('dashboard'));
        }

        return view('page.guidelines');
    }

    /**
     * Terms of Services
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function terms(Request $request)
    {
        return view('page.terms');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard(Request $request)
    {
        if (!$request->user()->terms_accepted_at) {
            return redirect('guidelines');
        }

        $total_episodes = Podcast::where('user_id', $request->user()->id)->sum('episodes');
        $latest_podcasts = Podcast::where('user_id', $request->user()->id)->orderBy('id', 'desc')->limit(3)->get();
        $total_vizzies = Vizzy::whereHas('podcast', function($query) use($request) {
           return $query->where('user_id', $request->user()->id);
        })->count();
        $latest_vizzies = Vizzy::whereHas('podcast', function($query) use($request) {
           return $query->where('user_id', $request->user()->id);
        })->orderBy('id', 'desc')->limit(3)->get();

        $data = [
            'podcast_claimed' => $request->user()->podcasts->count(),
            'total_episodes' => $total_episodes,
            'latest_podcasts' => $latest_podcasts,
            'total_vizzies' => $total_vizzies,
            'vizzy_interactions' => '-',
            'latest_vizzies' => $latest_vizzies
        ];

        return view('page.dashboard', compact('data'));
    }

    /**
     * Show the user profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile(Request $request)
    {
        if ($request->method() == 'POST') {
            $request->validate([
                'firstname'=>'required',
                'lastname'=>'required',
                'company'=>'required',
                'phone'=>'required',
                'password'=>['nullable','confirmed', new isValidPassword()]
            ]);

            try {
                $user = $request->user();
                $user->firstname =  $request->get('firstname');
                $user->lastname =  $request->get('lastname');
                $user->company =  $request->get('company');
                $user->phone =  $request->get('phone');
                if ($request->get('image')) {
                    $user->image =  $request->get('image');
                }
                if ($request->get('password')) {
                    $user->password = Hash::make($request->get('password'));
                }
                $user->save();
            } catch (\Exception $e) {
                // $v = Validator::make([], []);
                // $v->getMessageBag()->add('reseller', 'Reseller is required. You don\'t have permission to create global user.');
                // return back()->withErrors($v);
                return back()->with('error', $e);
            }

            return back()->with('success', 'Profile updated');

        }

        return view('page.profile');
    }

    /**
     * Help /Contact us page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function help(Request $request)
    {
        return view('page.help');
    }
}
