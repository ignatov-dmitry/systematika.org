<?php

namespace App\Http\Controllers;

use App\Models\MKWebhookLog;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MKWebhookLogController extends Controller
{
    public function list(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $logs = MKWebhookLog::select(['id', 'event', 'date_loaded', 'status', 'request', 'priority', 'date_create', 'run_time'])
            ->where('date_create', '>=', 1726952400);

        if ($request->has('status'))
        {
            $status = $request->get('status');
            $logs = $logs->where('status', '=', $status);
        }

        if ($request->has('search'))
        {
            $search = $request->get('search');
            $logs = $logs
                ->where(function (Builder $builder) use ($search){
                    $builder
                        ->where('request', 'LIKE', '%' . $search . '%');
                });

        }

        $statusCounts = MKWebhookLog::selectRaw('status, COUNT(*) as count')
            ->where('date_create', '>=', 1726952400)
            ->whereIn('status', ['processing', 'new', 'fail'])
            ->groupBy('status')
            ->pluck('count', 'status');


        $progressCount = $statusCounts['processing'] ?? 0;
        $newCount = $statusCounts['new'] ?? 0;
        $failCount = $statusCounts['fail'] ?? 0;

        $logWithMaxDifference = MKWebhookLog::select('id', 'date_create', 'date_loaded as dl',  DB::raw('ABS(unix_timestamp(now()) - date_create) as difference'))
            ->where('date_create', '>=', 1726952400)
            ->where('status', 'new')
            ->orderByDesc('difference')
            ->first();


        $subQuery = MKWebhookLog::select(
            'event',
            DB::raw('MAX(ABS(unix_timestamp(now()) - date_create)) as max_difference'),
            DB::raw('COUNT(*) as event_count')
        )
            ->where('date_create', '>=', 1726952400)
            ->where('status', 'new')
            ->groupBy('event');


        $logWithMaxDifferenceForWebhooks = MKWebhookLog::select(
            'logwebhook.event',
            'logwebhook.date_create',
            'logwebhook.date_loaded as dl',
            DB::raw('ABS(unix_timestamp(now()) - logwebhook.date_create) as difference'),
            'sub.event_count'
        )
            ->joinSub($subQuery, 'sub', function ($join) {
                $join->on('logwebhook.event', '=', 'sub.event')
                ->on(DB::raw('ABS(unix_timestamp(now()) - logwebhook.date_create)'), '=', 'sub.max_difference');
            })
            ->where('logwebhook.date_create', '>=', 1726952400)
            ->where('logwebhook.status', 'new')
            ->orderByDesc('difference')
            ->groupBy('logwebhook.event', 'logwebhook.date_create', 'logwebhook.date_loaded', 'sub.event_count')
            ->get();


        $subQuery = MKWebhookLog::select(
            'event',
            DB::raw('MAX(ABS(unix_timestamp(now()) - date_create)) as max_difference'),
            DB::raw('COUNT(*) as event_count')
        )
            ->where('date_create', '>=', 1726952400)
            ->groupBy('event');


        $logWithMaxDifferenceForWebhooksWeek = MKWebhookLog::select(
            'logwebhook.event',
            'logwebhook.date_create',
            'logwebhook.date_loaded as dl',
            DB::raw('ABS(unix_timestamp(now()) - logwebhook.date_create) as difference'),
            'sub.event_count',
            //DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND date_create >= unix_timestamp(DATE_SUB(now(), INTERVAL 7 DAY))) as count_last_7_days'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE()) as count_today'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 1 DAY) as count_yesterday'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 2 DAY) as count_day_2'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 3 DAY) as count_day_3'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 4 DAY) as count_day_4'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 5 DAY) as count_day_5'),
            DB::raw('(SELECT COUNT(*) FROM logwebhook as lw WHERE logwebhook.event = lw.event AND DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 6 DAY) as count_day_6')
        )
            ->joinSub($subQuery, 'sub', function ($join) {
                $join->on('logwebhook.event', '=', 'sub.event')
                    ->on(DB::raw('ABS(unix_timestamp(now()) - logwebhook.date_create)'), '=', 'sub.max_difference');
            })
            ->where('logwebhook.date_create', '>=', 1726952400)
            ->orderByDesc('difference')
            ->groupBy('logwebhook.event', 'logwebhook.date_create', 'logwebhook.date_loaded', 'sub.event_count')
            ->get();

        $logWithTotalForWebhooksWeek = MKWebhookLog::select(
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE()) as count_today'),
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 1 DAY) as count_day_1'),
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 2 DAY) as count_day_2'),
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 3 DAY) as count_day_3'),
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 4 DAY) as count_day_4'),
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 5 DAY) as count_day_5'),
            DB::raw('(SELECT COUNT(id) FROM logwebhook WHERE DATE(FROM_UNIXTIME(date_create)) = CURDATE() - INTERVAL 6 DAY) as count_day_6')
        )
            ->where('date_create', '>=', 1726952400)
            ->groupBy(['count_day_1'])
            ->first();

        $logs = $logs->orderBy('id', 'DESC')->paginate(25)->appends($request->query());
        return view(
            'logs.moyklass.list',
            compact('logs',
                'progressCount',
                'newCount',
                'failCount',
                'logWithMaxDifference',
                'logWithMaxDifferenceForWebhooks',
                'logWithMaxDifferenceForWebhooksWeek',
                'logWithTotalForWebhooksWeek'
            ));
    }

    public function info(MKWebhookLog $log)
    {
        $event = $log->event;
        $hookRequest = json_decode($log->request, true);

        $data = MKWebhookLog::getWebHookInfo($event, $hookRequest);

        return view('logs.moyklass.show', compact('log', 'data'));
    }
}
