<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int|null $userId
 * @property int|null $lessonId
 * @property int|null $free
 * @property int|null $visit
 * @property int|null $goodReason
 * @property int|null $test
 * @property int|null $skip
 * @property int|null $paid
 * @property string|null $createdAt
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereFree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereGoodReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord wherePaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereSkip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereTest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLessonRecord whereVisit($value)
 * @mixin \Eloquent
 */
class MKLessonRecord extends Model
{
    use HasFactory;

    protected $table = 'mk_lesson_records';
}
