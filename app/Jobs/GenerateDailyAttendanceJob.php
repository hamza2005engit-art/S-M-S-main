<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateDailyAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $sectionId,
        public string $date
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {

            $students = Student::where('section_id', $this->sectionId)->get();

              $students = Student::query()
                ->where('section_id', $this->sectionId)
                ->select('id', 'section_id')
                ->get();

            if ($students->isEmpty()) {
                return;
            }
            $rows = [];

            foreach ($students as $student) {

                $rows[] = [
                    'student_id' => $student->id,
                    'section_id' => $student->section_id,
                    'date' => $this->date,
                    'status' => 'present',
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ];
            }

            Attendance::upsert(
                $rows,
                [
                    'student_id',
                    'date'
                ],
                []
            );
        });
    }
}
