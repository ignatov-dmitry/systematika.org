<?php

namespace App\Http\Controllers;

use App\Models\GKUpdateLog;
use Illuminate\Http\Request;

class GKLogController extends Controller
{
    public function list()
    {
        $logs = GKUpdateLog::orderBy('date_create', 'DESC')->paginate(25);

        return view('logs.getcourse.list', compact('logs'));
    }

    public function info(GKUpdateLog $log)
    {
        $info = json_decode($log->request, true);
        return view('logs.getcourse.show', compact('log', 'info'));
    }
}
