<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailyAttendanceJob;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Console\Command;

class GenerateDailyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $sections = Section::pluck('id');
        foreach ($sections as $sectionId) {

            GenerateDailyAttendanceJob::dispatch($sectionId, $today);
        }
    }
}
