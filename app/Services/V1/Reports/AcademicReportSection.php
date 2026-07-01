<?php
namespace App\Services\Reports\Sections;

use App\Models\ExamResult;
use Carbon\Carbon;

class AcademicReportSection
{
    public function get(
        Carbon $start,
        Carbon $end
    ): array {

        $results = ExamResult::query()
            ->whereBetween('created_at', [$start, $end]);

        $averageMarks = round(
            $results->avg('mark'),
            2
        );

        $topStudents = ExamResult::query()
            ->with('student')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('mark')
            ->take(10)
            ->get();

        $failedStudents = ExamResult::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('mark', '<', 50)
            ->count();

        return [
            'average_marks' => $averageMarks,
            'failed_students' => $failedStudents,
            'top_students' => $topStudents,
        ];
    }
}
