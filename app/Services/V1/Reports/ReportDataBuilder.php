<?php

namespace App\Services\Reports;

use App\Services\Reports\AttendanceReportSection as ReportsAttendanceReportSection;
use App\Services\Reports\Sections\AttendanceReportSection;
use App\Services\Reports\Sections\AcademicReportSection;
use App\Services\Reports\Sections\FinancialReportSection;
use App\Services\Reports\Sections\SalaryReportSection;

class ReportDataBuilder
{
    public function build(
        $start,
        $end
    ): array {

        return [
 'period_start' => $start,

    'period_end' => $end,
            'attendance' =>
                app(ReportsAttendanceReportSection::class)
                    ->get($start, $end),

            'academic' =>
                app(AcademicReportSection::class)
                    ->get($start, $end),

            'financial' =>
                app(FinancialReportSection::class)
                    ->get($start, $end),

            'salary' =>
                app(SalaryReportSection::class)
                    ->get($start, $end),
        ];
    }
}
