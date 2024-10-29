<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int|null $teacher_id_mk
 * @property int|null $lesson_id_mk
 * @method static \Illuminate\Database\Eloquent\Builder|TeatchersLesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeatchersLesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeatchersLesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeatchersLesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeatchersLesson whereLessonIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeatchersLesson whereTeacherIdMk($value)
 * @mixin \Eloquent
 */
class TeatchersLesson extends Model
{
    use HasFactory;

    protected $table = 'teacherslesson';
}
