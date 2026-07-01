<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportBuilder
{
    public function generate(
        array $data,
        string $fileName
    ): string {
        $pdf = Pdf::loadView(
            'reports.school_report',
            [

                'period_start' =>
                $data['period_start'],

                'period_end' =>
                $data['period_end'],

                'attendance' =>
                $data['attendance'],

                'academic' =>
                $data['academic'],

                'financial' =>
                $data['financial'],

                'salary' =>
                $data['salary'],
            ]
        );
        $path = "reports/{$fileName}";

        $pdf->save(
            storage_path(
                "app/public/{$path}"
            )
        );

        return $path;
    }
}
