<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Services\V1\MyScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function createSchedule(Request $request)
    {
        $request->validate([
            'study_stage_id' => 'required|exists:study_stages,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year' => 'required|string',
            'semester' => 'required|in:1,2',
        ]);

        $schedule = Schedule::create([
            'study_stage_id' => $request->study_stage_id,
            'section_id' => $request->section_id,
            'academic_year' => $request->academic_year,
            'semester' => $request->semester,
        ]);
        $schedule->save();

        return response()->json(['data' => $schedule], 201);
    }

    public function getSchedule($id)
    {
        $schedule = Schedule::with('slots')->findOrFail($id);
        if(!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }
        return response()->json(['data' => $schedule], 200);
    }

    public function updateSchedule(Request $request, $id)
    {
        $request->validate([
            'study_stage_id' => 'sometimes|required|exists:study_stages,id',
            'section_id' => 'sometimes|required|exists:sections,id',
            'academic_year' => 'sometimes|required|string',
            'smester' => 'sometimes|required|in:1,2',
        ]);

        $schedule = Schedule::with('slots')->findOrFail($id);
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        if ($request->has('study_stage_id')) {
            $schedule->study_stage_id = $request->study_stage_id;
        }
        if ($request->has('section_id')) {
            $schedule->section_id = $request->section_id;
        }
        if ($request->has('academic_year')) {
            $schedule->academic_year = $request->academic_year;
        }
        if ($request->has('smester')) {
            $schedule->smester = $request->smester;
        }
        $schedule->save();

        return response()->json(['data' => $schedule], 200);
    }

    public function deleteSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }
        $schedule->delete();
        return response()->json(['message' => 'Schedule deleted successfully'], 200);
    }

    public function mySchedule($day = null){
        $user = auth('api')->user();
       // return $user ;
        if(!$day){
        $day = Carbon::now()->dayName ;
        }
        $schedules = app(MyScheduleService::class)->getMySchedule($user, $day);

         return response()->json(['data' => $schedules], 200);
    }
}
