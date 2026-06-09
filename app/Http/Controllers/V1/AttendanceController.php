<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Http\Resources\V1\AttendanceResource;
use App\Models\Attendance;
use App\Models\StudyStage;
use App\Services\V1\TakeBulkAttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private TakeBulkAttendanceService $bulkService;

    public function __construct(TakeBulkAttendanceService $bulkService)
    {
        $this->bulkService = $bulkService;
    }
    public function takeAttendance(AttendanceRequest $request)
    {
        $this->bulkService->takeBulkAttendance(
            $request->validated()
        );

        return response()->json([
            'message' => 'Attendance recorded successfully'
        ]);
    }

    public function getAttendance(int $section , int $studyStage)
    {
        $sectionId = StudyStage::where('stage_number', $studyStage)
            ->first()
            ->sections()
            ->where('section_number', $section)
            ->first()
            ->id;

        $date = Carbon::today();
        $attendances = Attendance::query()
            ->where('section_id', $sectionId)
            ->whereDate('date', $date)
            ->get();

        return AttendanceResource::collection($attendances);
    }
}
