<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Vizzy;
use Lukaswhite\PodcastFeedParser\Parser;
use App\WebServices\VizzyApi\VizzyApi;
use App\DataTables\VizzysDataTable;


class VizzyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    /**
     * Display a listing of vizzys.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, VizzysDataTable $dataTable)
    {
        // $status = $request->query('s');
        // if ($status) {
        //     $vizzys = Vizzy::where('status', $status)->orderBy('id', 'desc')->get();
        // } else {
        //     $vizzys = Vizzy::orderBy('id', 'desc')->get();
        // }

        //return view('admin.vizzys.index', compact('vizzys', 'status'));
        return $dataTable->render('admin.vizzys.index');
    }

    /**
     * approve a pending vizzy.
     *
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Vizzy $vizzy, VizzyApi $vizzyApi)
    {
        if (!$request->user()->can('approve-vizzy')) {
            abort(403);
        }

        $vizzy->status = Vizzy::STATUS_PUBLISHED;
        $vizzy->published_at = now();
        $vizzy->save();

        // Notify api of vizzy published
        $vizzyApi->publish($vizzy->episode_guid);

        return back()->with('success', 'Vizzy has been published');
    }

    /**
     * reject a pending vizzy.
     *
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, Vizzy $vizzy)
    {
        if (!$request->user()->can('approve-vizzy')) {
            abort(403);
        }

        $vizzy->status = Vizzy::STATUS_REJECTED;
        $vizzy->save();

        return back()->with('success', 'Vizzy has been rejected');
    }

}
