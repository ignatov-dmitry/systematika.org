<?php

namespace App\Http\Controllers;

use App\Models\MKWebhookLog;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class MKWebhookLogController extends Controller
{
    public function list(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $logs = MKWebhookLog::select(['id', 'event', 'date_loaded', 'status', 'request'])
            ->where('date_create', '>=', 1726952400);

        if ($request->has('status'))
        {
            $status = $request->get('status');
            $logs = $logs->where('status', '=', $status);
        }


        $logs = $logs->orderBy('id', 'DESC')->paginate(25)->appends($request->query());
        return view('logs.moyklass.list', compact('logs'));
    }

    public function info(MKWebhookLog $log)
    {
        $event = $log->event;
        $hookRequest = json_decode($log->request, true);

        $data = MKWebhookLog::getWebHookInfo($event, $hookRequest);

        return view('logs.moyklass.show', compact('data'));
    }
}
