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

    const LESSON_RECORD_NEW = 'lesson_record_new';
    const LESSON_NEW = 'lesson_new';
    const LESSON_START = 'lesson_start';
    const JOIN_CHANGED = 'join_changed';
    const LESSON_RECORD_CHANGED = 'lesson_record_changed';
    const LESSON_CHANGED = 'lesson_changed';
    const USER_CHANGED_STATE = 'user_changed_state';
    const USER_CHANGED = 'user_changed';
    const JOIN_CHANGED_STATE = 'join_changed_state';
    const LESSON_RECORD_DELETED = 'lesson_record_deleted';
    const LESSON_DELETED = 'lesson_deleted';
    const CLASS_CHANGED = 'class_changed';

    public static array $events = [
        self::LESSON_RECORD_NEW => 'Создана запись на занятие',
        self::LESSON_NEW => 'Новое занятие',
        self::LESSON_START => 'Начало занятия',
        self::JOIN_CHANGED => 'Информация о записи в группу изменилась',
        self::LESSON_RECORD_CHANGED => 'Информация о записи на занятие изменилась',
        self::LESSON_CHANGED => 'Информация о занятии изменилась',
        self::USER_CHANGED_STATE => 'Статус ученика изменился',
        self::USER_CHANGED => 'Информация об ученике изменилась',
        self::JOIN_CHANGED_STATE => 'Статус записи в группу изменился',
        self::LESSON_RECORD_DELETED => 'Запись на занятие удалена',
        self::LESSON_DELETED => 'Занятие удалено',
        self::CLASS_CHANGED => 'Информация о группе изменилась',
    ];

    public static function getEvents(): array
    {
        return self::$events;
    }

    public static function getEventName($slug)
    {
        return self::$events[$slug];
    }

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

    private static function join_changed($request): array
    {
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::select(['mk_users.*', 'm.gk_uid'])
            ->where('mk_users.id', '=', $userId)
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->get();

        return $data;
    }

    private static function lesson_changed($request): array
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*', 'm.gk_uid'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    private static function lesson_start($request): array
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*', 'm.gk_uid'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    private static function lesson_record_deleted($request): array
    {
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::select(['mk_users.*', 'm.gk_uid'])
            ->where('id', '=', $userId)
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->get();

        return $data;
    }

    private static function lesson_deleted($request): array
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*', 'm.gk_uid'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    private static function user_changed($request): array
    {
        $data = array();
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::select(['mk_users.*', 'm.gk_uid'])
            ->where('id', '=', $userId)
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->get();

        return $data;
    }

    private static function user_changed_state($request): array
    {
        $data = array();
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::select(['mk_users.*', 'm.gk_uid'])
            ->where('id', '=', $userId)
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->get();

        return $data;
    }

    private static function join_changed_state($request): array
    {
        $data = array();
        $userId = $request['object']['userId'];

        $data['users'] = MKUser::select(['mk_users.*', 'm.gk_uid'])
            ->where('id', '=', $userId)
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->get();

        return $data;
    }

    private static function lesson_record_new($request): array
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*', 'm.gk_uid'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }

    public static function lesson_record_changed($request): array
    {
        $lessonRecordId = $request['object']['lessonRecordId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*', 'm.gk_uid'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->where('mlr.id', '=', $lessonRecordId)
            ->get();

        return $data;
    }

    private static function lesson_new($request): array
    {
        $lessonId = $request['object']['lessonId'];

        $data['users'] = MKUser::query()
            ->select(['mk_users.*', 'm.gk_uid'])
            ->leftJoin('mk_lesson_records as mlr', 'mlr.userId', '=', 'mk_users.id')
            ->leftJoin('member as m', 'm.mk_uid', '=', 'mk_users.id')
            ->where('mlr.lessonId', '=', $lessonId)
            ->get();

        return $data;
    }
}
