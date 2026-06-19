<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailyAttendanceJob;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Student;
use App\Services\Reports\MonthlyReportGenerator;
use Illuminate\Console\Command;

class GenerateMonthlyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly report for attendance';

    /**
     * Execute the console command.
     */
   public function handle()
{
    app(
        MonthlyReportGenerator::class
    )->generate();

    $this->info(
        'Report Generated'
    );
}
}
