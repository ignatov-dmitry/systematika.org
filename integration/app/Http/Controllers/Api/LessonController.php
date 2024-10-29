<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberLessonResource;
use App\Models\LessonRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LessonController extends Controller
{
    public function memberLesson(Request $request)
    {
        $email = $request->get('email');
        $gkId = $request->get('gk_id');

        $lessonsRecord = LessonRecord::select(['recordslesson.*'])
            ->leftJoin('member as m', 'm.mk_uid', '=', 'recordslesson.user_id_mk')
            ->join('lessons as l', 'l.lesson_id_mk', '=', 'recordslesson.lesson_id_mk')
            ->where(function (Builder $builder) use ($email, $gkId) {
                $builder->where('m.email', $email)
                    ->orWhere('m.gk_uid', $gkId);
            })
            ->where(function (Builder $query) {
                $query->where('l.date', '>', Carbon::today())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('l.date', '=', Carbon::today())
                            ->where('l.begin_time', '>', Carbon::now()->format('H:i:s'));
                    });
            })
            ->orderBy('l.date')
            ->orderBy('l.begin_time')
            ->with(['lesson'])
            ->first();


        return new MemberLessonResource($lessonsRecord);
    }
}
