<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property int|null $record_id_mk
 * @property int|null $lesson_id_mk
 * @property int|null $user_id_mk
 * @property int|null $free
 * @property int|null $test
 * @property int|null $skip
 * @property int|null $visit
 * @property int|null $paid
 * @property int|null $good_reason
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereGoodReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereLessonIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereRecordIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereSkip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereTest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereUserIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonRecord whereVisit($value)
 * @property-read \App\Models\Lesson|null $lesson
 * @property-read \App\Models\Member|null $member
 * @mixin \Eloquent
 */
class LessonRecord extends Model
{
    use HasFactory;

    protected $table = 'recordslesson';

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'user_id_mk', 'mk_uid');
    }

    public function lesson(): HasOne
    {
        return $this->hasOne(Lesson::class, 'lesson_id_mk', 'lesson_id_mk');
    }
}
