<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportAppActivityLogs;
use App\Exports\ExportAppPlayed;
use App\Exports\ExportAppUsers;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function index()
    {
        return view('admin.export');
    }

    public function exportAppUsers()
    {
        return Excel::download(new ExportAppUsers, 'users.csv');
    }

    public function exportAppActivityLogs()
    {
        return Excel::download(new ExportAppActivityLogs, 'activity-log.csv');
    }

    public function exportAppPlayed()
    {
        return Excel::download(new ExportAppPlayed, 'played.csv');
    }
}
