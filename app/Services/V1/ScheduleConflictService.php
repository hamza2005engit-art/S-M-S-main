<?php

namespace App\Services\V1;

use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleConflictService
{

    public function validate(
        Schedule $schedule,
        array $createdSlots = [],
        array $updatedSlots = [],
        array $deletedSlotIds = []
    ): void {

        $incomingSlots = [
            ...$createdSlots,
            ...$updatedSlots,
        ];

        $teachers = Teacher::query()

            ->with([
                'materials:id',
                'sections:id',
            ])

            ->whereIn(
                'id',
                collect($incomingSlots)
                    ->pluck('teacher_id')
                    ->unique()
            )

            ->get()
            ->keyBy('id');

        $existingSlots = ScheduleSlot::query()

            ->whereNotIn(
                'id',
                $deletedSlotIds
            )
            ->get();

        $teacherPeriodMap = [];

        $sectionPeriodMap = [];

        foreach ($existingSlots as $slot) {

            $teacherKey = $this->teacherConflictKey(
                $slot->teacher_id,
                $slot->day,
                $slot->period_id
            );

            $teacherPeriodMap[$teacherKey] = true;

            $sectionKey = $this->sectionConflictKey(
                $slot->schedule_id,
                $slot->day,
                $slot->period_id
            );

            $sectionPeriodMap[$sectionKey] = true;
        }

        foreach ($incomingSlots as $slot) {

            $teacher = $teachers[
                $slot['teacher_id']
            ];

            $canTeachMaterial = $teacher->materials
                ->contains(
                    'id',
                    $slot['material_id']
                );

            if (! $canTeachMaterial) {

                throw ValidationException::withMessages([

                    'teacher_material' => [

                        "Teacher {$teacher->id} cannot teach material {$slot['material_id']}"

                    ]

                ]);
            }

            $canTeachSection = $teacher->sections
                ->contains(
                    'id',
                    $schedule->section_id
                );

            if (! $canTeachSection) {

                throw ValidationException::withMessages([

                    'teacher_section' => [

                        "Teacher {$teacher->id} is not assigned to this section"

                    ]

                ]);
            }

            $teacherKey = $this->teacherConflictKey(
                $slot['teacher_id'],
                $slot['day'],
                $slot['period_id']
            );

            if (isset($teacherPeriodMap[$teacherKey])) {


                if (
                    isset($slot['id']) &&
                    $this->isSameTeacherSlot(
                        $existingSlots,
                        $slot
                    )
                ) {


                } else {

                    throw ValidationException::withMessages([

                        'teacher_conflict' => [

                            "Teacher already assigned at day {$slot['day']} period {$slot['period_id']}"

                        ]

                    ]);
                }
            }

            $sectionKey = $this->sectionConflictKey(
                $schedule->id,
                $slot['day'],
                $slot['period_id']
            );

            if (isset($sectionPeriodMap[$sectionKey])) {

                if (
                    isset($slot['id']) &&
                    $this->isSameSectionSlot(
                        $existingSlots,
                        $slot
                    )
                ) {



                } else {

                    throw ValidationException::withMessages([

                        'section_conflict' => [

                            "Section already has slot at day {$slot['day']} period {$slot['period_id']}"

                        ]

                    ]);
                }
            }

            $teacherPeriodMap[$teacherKey] = true;

            $sectionPeriodMap[$sectionKey] = true;
        }
    }

    private function teacherConflictKey(
        int $teacherId,
        string $day,
        int $periodId
    ): string {

        return "{$teacherId}-{$day}-{$periodId}";
    }

    private function sectionConflictKey(
        int $scheduleId,
        string $day,
        int $periodId
    ): string {

        return "{$scheduleId}-{$day}-{$periodId}";
    }

    private function isSameTeacherSlot(
        $existingSlots,
        array $slot
    ): bool {

        return $existingSlots

            ->where('id', $slot['id'])

            ->where('teacher_id', $slot['teacher_id'])

            ->where('day', $slot['day'])

            ->where('period_id', $slot['period_id'])

            ->isNotEmpty();
    }

    private function isSameSectionSlot(
        $existingSlots,
        array $slot
    ): bool {

        return $existingSlots

            ->where('id', $slot['id'])

            ->where('schedule_id', $slot['schedule_id'] ?? null)

            ->where('day', $slot['day'])

            ->where('period_id', $slot['period_id'])

            ->isNotEmpty();
    }
}

