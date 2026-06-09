<?php

namespace App\Services\V1;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TakeBulkAttendanceService
{
    public function takeBulkAttendance(array $attendanceData): void
    {
        DB::transaction(function () use ($attendanceData) {

            $user = auth('api')->user();
            $studentIds = collect($attendanceData['students'])
                ->pluck('student_id');

            $attendances = Attendance::query()
                ->where('section_id', $attendanceData['section_id'])
                ->whereDate('date', Carbon::today())
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');

            $rows = [];
            foreach ($attendanceData['students'] as $student) {

                $attendance = $attendances
                    ->get($student['student_id']);

                if (! $attendance) {
                    continue;
                }

                $rows[] = [
                    'id' => $attendance->id,
                    'status' => $student['status'],
                    'updated_at' => now(),
                ];
            }

            foreach ($attendanceData['students'] as $student) {

                $attendance = $attendances
                    ->get($student['student_id']);

                if (! $attendance) {
                    continue;
                }

                $attendance->update([
                    'status' => $student['status'],
                ]);
            }
        });
    }
}
