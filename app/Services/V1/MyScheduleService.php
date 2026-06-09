<?php

namespace App\Services\V1;

use App\Models\User;
use App\Models\ScheduleSlot;

class MyScheduleService
{
    public function getMySchedule(User $user,string $day)
    {

        if ($user->hasRole('student')) {

            $student = $user->student;

            return ScheduleSlot::query()
                ->with([
                    'period:id,name,start_time,end_time',
                    'teacher.user:id',
                    'material:id,name',
                ])
                ->whereHas('schedule', function ($query) use ($student) {

                    $query->where(
                        'section_id',
                        $student->section_id
                    );
                })
                ->where('day', $day)
                ->orderBy('period_id')
                ->get()
                ->map(fn ($slot) => [

                    'id' => $slot->id,

                    'period' => $slot->period->name,

                    'start_time' =>
                        $slot->period->start_time,

                    'end_time' =>
                        $slot->period->end_time,

                    'material' =>
                        $slot->material->name,

                    'teacher' =>
                        $slot->teacher->user->name,
                ]);
        }

        if ($user->hasRole('teacher')) {

            $teacher = $user->teacher;

            return ScheduleSlot::query()
                ->with([
                    'period:id,name,start_time,end_time',
                    'material:id,name',
                    'schedule.section:id,section_number',
                    'schedule.studyStage:id,stage_number',
                ])
                ->where('teacher_id',$teacher->id)
                ->where('day',$day)
                ->orderBy('period_id')
                ->get()
                ->map(fn ($slot) => [

                    'id' => $slot->id,

                    'period' =>
                        $slot->period->name,

                    'start_time' =>
                        $slot->period->start_time,

                    'end_time' =>
                        $slot->period->end_time,

                    'material' =>
                        $slot->material->name,

                    'section' =>
                        $slot->schedule?->section?->section_number,
                    'study_stage' =>
                        $slot->schedule?->studyStage?->stage_number,
                ]);
        }

        return [];
    }
}
