<?php
namespace App\Services\Reports;

use App\Models\Report;
use App\Enums\ReportType;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceReportSection
{
    public function get(
        Carbon $start,
        Carbon $end
    ): array {

        $total = Attendance::whereBetween(
            'attendance_date',
            [$start,$end]
        )->count();

        $present = Attendance::whereBetween(
            'attendance_date',
            [$start,$end]
        )
        ->where('status','present')
        ->count();

        $absent = Attendance::whereBetween(
            'attendance_date',
            [$start,$end]
        )
        ->where('status','absent')
        ->count();

        $excused = Attendance::whereBetween(
            'attendance_date',
            [$start,$end]
        )
        ->where('status','excused')
        ->count();
        return [
            'total'=>$total,
            'present'=>$present,
            'absent'=>$absent,
            'excused'=>$excused,
        ];
    }
}
