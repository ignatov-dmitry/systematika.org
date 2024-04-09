<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string|null $event
 * @property string|null $request
 * @property int|null $date_create
 * @property string|null $status
 * @property int|null $date_loaded
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog whereDateCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog whereDateLoaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog whereRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKWebhookLog whereStatus($value)
 * @mixin \Eloquent
 */
class MKWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'logwebhook';

    public function getDates()
    {
        return [
            'date_loaded'
        ];
    }

    public static function getWebHookInfo($event, $request)
    {
        return self::$event($request);
    }

    private static function join_changed($request)
    {
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::where('id', '=', $userId)->get();

        return $data;
    }

    private static function lesson_changed($request)
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    private static function lesson_start($request)
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    private static function lesson_record_deleted($request)
    {
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::where('id', '=', $userId)->get();

        return $data;
    }

    private static function lesson_deleted($request)
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    private static function user_changed($request)
    {
        $data = array();
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::where('id', '=', $userId)->get();

        return $data;
    }
}
