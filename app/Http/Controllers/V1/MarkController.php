<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\Teacher;
use Illuminate\Http\Request;

class MarkController extends Controller
{
     public function index(Request $request)
    {
        // 🔥 JWT user
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }

        $marks = Mark::with(['student.user', 'material'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('student_id')) {
            $marks->where('student_id', $request->student_id);
        }

        if ($request->filled('material_id')) {
            $marks->where('material_id', $request->material_id);
        }

        if ($request->filled('type')) {
            $marks->where('type', $request->type);
        }

        return response()->json([
            'marks' => $marks->get()
        ]);
    }














public function teacherMarks(Request $request)
{
    $user = auth('api')->user();

    $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

    $marks = Mark::with(['student.user', 'material'])
        ->where('teacher_id', $teacher->id)
        ->get()
        ->groupBy(['student_id', 'material_id']);

    $result = [];

    foreach ($marks as $studentId => $materials) {
        foreach ($materials as $materialId => $items) {

            $exercise = $items->where('type', 'exercise')->sum('score');
            $mid      = $items->where('type', 'test')->sum('score');
            $final    = $items->where('type', 'final')->sum('score');

            $total = $exercise + $mid + $final;

            $result[] = [
                'student_id' => $studentId,
                'student_name' => $items->first()->student->user->full_name,
                'material_name' => $items->first()->material->name,
                'exercise' => $exercise,
                'mid' => $mid,
                'final' => $final,
                'total' => $total,
            ];
        }
    }

    return response()->json($result);
}


public function store(Request $request)
{
    $user = auth('api')->user();

    $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

    $request->validate([
        'student_id' => 'required|exists:students,id',
        'material_id' => 'required|exists:materials,id',
        'type' => 'required|in:exercise,test,final',
        'score' => 'required|integer|min:0|max:100',
    ]);

    $mark = Mark::create([
        'student_id' => $request->student_id,
        'teacher_id' => $teacher->id,   
        'material_id' => $request->material_id,
        'type' => $request->type,
        'score' => $request->score,
    ]);

    return response()->json([
        'message' => 'Mark added successfully',
        'data' => $mark
    ]);
}
public function updateByStudent(Request $request)
{
    $user = auth('api')->user();

    $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

    $request->validate([
        'student_id' => 'required|exists:students,id',
        'material_id' => 'required|exists:materials,id',
        'type' => 'required|in:exercise,test,final',
        'score' => 'required|integer|min:0|max:100',
    ]);

    $mark = Mark::where('teacher_id', $teacher->id)
        ->where('student_id', $request->student_id)
        ->where('material_id', $request->material_id)
        ->where('type', $request->type)
        ->first();

    if (!$mark) {
        return response()->json([
            'message' => 'Mark not found for this student/material/type'
        ], 404);
    }

    $mark->update([
        'score' => $request->score
    ]);

    return response()->json([
        'message' => 'Mark updated successfully',
        'data' => $mark
    ]);
}
public function studentReport($student_id)
{
    $user = auth('api')->user();

    $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

    $student = \App\Models\Student::with(['user', 'section.studyStage'])
        ->findOrFail($student_id);

    $marks = Mark::with(['material', 'student.user'])
        ->where('teacher_id', $teacher->id)
        ->where('student_id', $student_id)
        ->get()
        ->groupBy('material_id');

    $result = [];
    $overallTotal = 0;
    $subjectCount = 0;

    foreach ($marks as $materialId => $items) {

        $exercise = $items->where('type', 'exercise')->sum('score');
        $test     = $items->where('type', 'test')->sum('score');
        $final    = $items->where('type', 'final')->sum('score');

        $total = $exercise + $test + $final;

        $subjectName = $items->first()?->material?->name;

        $result[] = [
            'material_id' => $materialId,
            'material_name' => $subjectName,
            'exercise' => $exercise,
            'test' => $test,
            'final' => $final,
            'total' => $total,
            'average' => round($total / 3, 2),
        ];

        $overallTotal += $total;
        $subjectCount++;
    }

    $finalAverage = $subjectCount > 0 ? $overallTotal / $subjectCount : 0;

    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->user->full_name ?? $student->user->name,
            'stage' => $student->section->studyStage->stage_number,
            'section' => $student->section->section_number,
        ],

        'subjects' => $result,

        'final_average' => round($finalAverage, 2),
    ]);
}
public function myMarks()
{
    
    $user = auth('api')->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

     
    $student = \App\Models\Student::where('user_id', $user->id)->first();

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $marks = \App\Models\Mark::with(['material', 'teacher'])
        ->where('student_id', $student->id)
        ->get()
        ->groupBy('material_id');

    $result = [];

    foreach ($marks as $materialId => $items) {

        $exercise = $items->where('type', 'exercise')->sum('score');
        $test     = $items->where('type', 'test')->sum('score');
        $final    = $items->where('type', 'final')->sum('score');

        $total = $exercise + $test + $final;

        $result[] = [
            'material_id' => $materialId,
            'material_name' => $items->first()->material->name ?? null,
            'teacher_name' => $items->first()->teacher->name ?? null,
            'exercise' => $exercise,
            'test' => $test,
            'final' => $final,
            'total' => $total,
            'average' => round($total / 3, 2),
        ];
    }

    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->user->full_name ?? $student->user->name,
        ],
        'marks' => $result
    ]);
}
}
