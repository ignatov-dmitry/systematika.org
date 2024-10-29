<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property int|null $lesson_id_mk
 * @property int|null $class_id_mk
 * @property int|null $course_id_mk
 * @property string|null $class_name
 * @property string|null $course_name
 * @property int|null $timestart
 * @property string|null $begin_time
 * @property string|null $end_time
 * @property string|null $date
 * @property string|null $description
 * @property string|null $topic
 * @property string|null $status
 * @property int|null $room_id_mk
 * @property int|null $videorecord
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereBeginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereClassIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereClassName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCourseIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCourseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereLessonIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereRoomIdMk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereTimestart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereVideorecord($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LessonRecord> $lessonRecords
 * @property-read int|null $lesson_records_count
 * @mixin \Eloquent
 */
class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lessons';

    public function lessonRecords(): HasMany
    {
        return $this->hasMany(LessonRecord::class, 'lesson_id_mk', 'lesson_id_mk')->with(['member']);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(TeatchersLesson::class, 'lesson_id_mk', 'lesson_id_mk');
    }
}
