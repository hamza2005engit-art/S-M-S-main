<?php

namespace App\Jobs;

use App\Services\Reports\FourMonthsReportGenerator;
use App\Services\Reports\MonthlyReportGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportsJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (today()->isLastOfMonth()) {

            app(MonthlyReportGenerator::class)
                ->generate();
        }

        $month = now()->month;

        if (
            today()->isLastOfMonth()
            &&
            in_array($month, [4, 8, 12])
        ) {
            app(FourMonthsReportGenerator::class)
                ->generate();
        }
    }
}
