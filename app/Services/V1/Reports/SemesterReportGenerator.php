<?php

namespace App\Services\Reports;

use App\Models\Report;
use App\Enums\ReportType;

class FourMonthsReportGenerator
{
    public function generate(): Report
    {
        $start = now()
            ->subMonths(3)
            ->startOfMonth();

        $end = now()
            ->endOfMonth();
        // Collect Report Data
        $data = app(
            ReportDataBuilder::class
        )->build(
            $start,
            $end
        );

        // Generate PDF
        $filePath = app(
            PdfReportBuilder::class
        )->generate(
            $data,
            'semester_' . now()->format('Y_m') . '.pdf'
        );

        // Save Report Record
        return Report::create([
            'type' => ReportType::MONTHLY,
            'period_start' => $start,
            'period_end' => $end,
            'file_path' => $filePath,
            'generated_at' => now(),
            'statistics' => [
                'attendance' => $data['attendance'],
                'academic' => [
                    'average_marks' =>
                    $data['academic']['average_marks']
                ],
                'financial' => [
                    'revenue' =>
                    $data['financial']['total_revenue']
                ],
                'salary' => [
                    'total_salary' =>
                    $data['salary']['total_salary']
                ]
            ]
        ]);
    }
}
