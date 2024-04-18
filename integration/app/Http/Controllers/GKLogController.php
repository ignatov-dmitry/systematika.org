<?php

namespace App\Http\Controllers;

use App\Models\GKUpdateLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GKLogController extends Controller
{
    public function list(Request $request)
    {

        $minDateTimestamp = Carbon::parse($request->get('date'))
            ->hour(0)
            ->setMinute(0)
            ->setSecond(0)
            ->timestamp;

        $maxDateTimestamp = Carbon::parse($request->get('date'))
            ->hour(23)
            ->setMinute(59)
            ->setSecond(59)
            ->timestamp;

        $logs = GKUpdateLog::with(['mk_user', 'integration_user'])
            ->orderBy('date_create', 'DESC')
            ->where('date_create', '>=', $minDateTimestamp)
            ->where('date_create', '<=', $maxDateTimestamp)
            ->paginate(25)
            ->withQueryString();

        return view('logs.getcourse.list', compact('logs'));
    }

    public function info(GKUpdateLog $log)
    {
        $info = json_decode($log->request, true);
        return view('logs.getcourse.show', compact('log', 'info'));
    }
}
