<?php

namespace App\Http\Resources;

use App\Services\MoyKlassApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class MemberLessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $managers = Cache::remember('managers', 360 * 60, function (){
            $moyKlassApiService = new MoyKlassApiService();
            return $moyKlassApiService->call('company/managers');
        });

        $managers = collect($managers)
            ->whereIn('id', $this->lesson->teachers()->pluck('teacher_id_mk')->toArray())->pluck('name')->toArray();


        return [
            'url'               => $this->lesson->topic,
            'class_name'        => $this->lesson->class_name,
            'teacher'           => $managers,
            'lesson_date'       => $this->lesson->date,
            'lesson_start_time' => $this->lesson->begin_time
        ];
    }
}
