<?php

namespace App\Services\V1;

use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Models\Teacher;
use App\Services\V1\ScheduleConflictService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleSlotSyncService
{
    private ScheduleConflictService $conflictService;

    public function __construct(ScheduleConflictService $conflictService)
    {
        $this->conflictService = $conflictService;
    }

    public function sync(array $data): void
    {
        $schedule = Schedule::findOrFail(
            $data['schedule_id']
        );

        $this->conflictService->validate(
            schedule: $schedule,
            createdSlots: $data['created_slots'] ?? [],
            updatedSlots: $data['updated_slots'] ?? [],
            deletedSlotIds: $data['deleted_slot_ids'] ?? [],
        );

        DB::transaction(function () use ($data, $schedule) {

            $this->deleteSlots(
                $data['deleted_slot_ids'] ?? []
            );

            $this->updateSlots(
                $data['updated_slots'] ?? []
            );

            $this->createSlots(
                $schedule,
                $data['created_slots'] ?? []
            );
        });
    }

    private function deleteSlots(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        ScheduleSlot::whereIn('id', $ids)
            ->destroy();
    }

    private function updateSlots(array $slots): void
    {
        foreach ($slots as $slotData) {

            $slot = ScheduleSlot::findOrFail(
                $slotData['id']
            );

            $slot->update([
                'day' => $slotData['day'],
                'period_id' => $slotData['period_id'],
                'teacher_id' => $slotData['teacher_id'],
                'material_id' => $slotData['material_id'],
            ]);
        }
    }

    private function createSlots(
        Schedule $schedule,
        array $slots
    ): void {

        if (empty($slots)) {
            return;
        }

        $schedule->slots()->createMany($slots);
    }

}
