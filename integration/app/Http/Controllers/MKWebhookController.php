<?php

namespace App\Http\Controllers;

use App\Models\MKWebhookLog;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class MKWebhookController extends Controller
{
    public function list(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $logs = MKWebhookLog::select(['id', 'event', 'date_loaded'])->orderBy('date_loaded', 'DESC')->paginate(25);
        return view('logs.moyklass.list', compact('logs'));
    }

    public function hookInfo(MKWebhookLog $log)
    {
        $event = $log->event;
        $hookRequest = json_decode($log->request, true);

        $data = MKWebhookLog::getWebHookInfo($event, $hookRequest);

        return view('logs.moyklass.show', compact('data'));
    }
}
