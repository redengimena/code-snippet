<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Podcast;
use App\Models\Vizzy;
use App\Models\User;


class DashboardController extends Controller
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
     * Display a admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $vizzy_started = Vizzy::count();
        $vizzy_published = Vizzy::where('status', Vizzy::STATUS_PUBLISHED)->count();

        $latest_podcasts = Podcast::where('user_id','!=',0)->orderBy('id', 'desc')->limit(3)->get();
        $latest_vizzies = Vizzy::orderBy('id', 'desc')->limit(3)->get();
        $latest_podcasters = User::orderBy('id','desc')->limit(5)->get();

        $usage_stats = $this->getUsageStats();

        $data = [
            'podcaster' => User::count(),
            'podcast_claimed' => Podcast::count(),
            'vizzy_started' => $vizzy_started,
            'vizzy_published' => $vizzy_published,
            'latest_podcasts' => $latest_podcasts,
            'latest_vizzies' => $latest_vizzies,
            'latest_podcasters' => $latest_podcasters,
            'usage_stats' => $usage_stats
        ];
        return view('admin.dashboard', compact('data'));
    }


    public function getUsageStats(){
        $data = [
          'activeUsers' => $this->getActiveUsers(),
          'totalPlays' => $this->getTotalPlays(),
          'vizzyPlays' => $this->getVizzyPlays(),
        ];
        return $data;
    }

    public function getActiveUsers()
    {
        $data = [];
        $result = DB::connection('vizzyapi')->select("
            SELECT
              date_format(created_at, '%b') as month,
              count(distinct user_id) as total
            FROM `played`
            WHERE created_at > DATE_SUB(now(), INTERVAL 6 MONTH)
            GROUP BY date_format(created_at, '%b'), date_format(created_at, '%Y%m')
            ORDER BY date_format(created_at, '%Y%m')
        ");
        foreach ($result as $row) {
          $data[$row->month] = $row->total;
        }

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $month = date('M', strtotime("-$i month"));
            $months[$month] = array_key_exists($month, $data) ? $data[$month] : 0;
        }

        $months = array_reverse($months);

        return json_encode($months);
    }

    public function getTotalPlays()
    {
        $data = [];
        $result = DB::connection('vizzyapi')->select("
            SELECT
              date_format(created_at, '%b') as month,
              count(*) as total
            FROM `played`
            WHERE created_at > DATE_SUB(now(), INTERVAL 6 MONTH)
            GROUP BY date_format(created_at, '%b'), date_format(created_at, '%Y%m')
            ORDER BY date_format(created_at, '%Y%m')
        ");
        foreach ($result as $row) {
          $data[$row->month] = $row->total;
        }

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $month = date('M', strtotime("-$i month"));
            $months[$month] = array_key_exists($month, $data) ? $data[$month] : 0;
        }

        $months = array_reverse($months);

        return json_encode($months);
    }

    public function getVizzyPlays()
    {
        $data = [];
        $result = DB::connection('vizzyapi')->select("
            SELECT
              date_format(created_at, '%b') as month,
              count(*) as total
            FROM `played`
            WHERE created_at > DATE_SUB(now(), INTERVAL 6 MONTH)
            AND has_vizzy = 1
            GROUP BY date_format(created_at, '%b'), date_format(created_at, '%Y%m')
            ORDER BY date_format(created_at, '%Y%m')
        ");
        foreach ($result as $row) {
          $data[$row->month] = $row->total;
        }

        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $month = date('M', strtotime("-$i month"));
            $months[$month] = array_key_exists($month, $data) ? $data[$month] : 0;
        }

        $months = array_reverse($months);

        return json_encode($months);
    }

}
