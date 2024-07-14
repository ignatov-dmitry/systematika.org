<?php

namespace App\Http\Controllers;

use App\Models\GKUpdateLog;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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

        $logs = GKUpdateLog::query()
            ->with(['mk_user', 'integration_user'])
            ->orderBy('date_create', 'DESC');

        if ($email = $request->get('email'))
            $logs->where('email', '=', $email);
        else
        {
            $logs
                ->where('date_create', '>=', $minDateTimestamp)
                ->where('date_create', '<=', $maxDateTimestamp);
        }
        $logs = $logs
            ->paginate(25)
            ->withQueryString();

        return view('logs.getcourse.list', compact('logs'));
    }

    public function info(GKUpdateLog $log): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $info = json_decode($log->request, true);
        return view('logs.getcourse.show', compact('log', 'info'));
    }
}
