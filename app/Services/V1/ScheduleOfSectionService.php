<?php

namespace App\Services\V1;

use App\Models\Schedule;
use App\Models\ScheduleSlot;

class ScheduleOfSectionService
{
    public function getScheduleOfSection($section_id, $study_stage_id)
    {
        return ScheduleSlot::query()
            ->with([
                'period:id,name,start_time,end_time',
                'material:id,name',
                'teacher.user:id,name',
            ])
            ->whereHas('schedule', function ($query) use ($section_id, $study_stage_id) {
                $query->where('section_id', $section_id)
                      ->where('study_stage_id', $study_stage_id);
            })
            ->orderBy('day')
            ->orderBy('period_id')
            ->get()
            ->groupBy('day')
            ->map(function ($slots, $day) {
                return [
                    'day' => $day,
                    'slots' => $slots->map(function ($slot) {
                        return [
                            'id' => $slot->id,
                            'period' => $slot->period->name,
                            'start_time' => $slot->period->start_time,
                            'end_time' => $slot->period->end_time,
                            'material' => $slot->material->name,
                            'teacher' => $slot->teacher->user->name,
                        ];
                    }),
                ];
            })
            ->values();
    }
}
