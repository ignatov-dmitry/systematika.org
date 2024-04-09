<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string|null $date
 * @property string|null $beginTime
 * @property string|null $endTime
 * @property string|null $createdAt
 * @property int|null $filialId
 * @property int|null $roomId
 * @property int|null $classId
 * @property int|null $status
 * @property string|null $comment
 * @property int|null $maxStudents
 * @property string|null $topic
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereBeginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereFilialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereMaxStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKLesson whereTopic($value)
 * @mixin \Eloquent
 */
class MKLesson extends Model
{
    use HasFactory;

    protected $table = 'mk_lessons';
}
