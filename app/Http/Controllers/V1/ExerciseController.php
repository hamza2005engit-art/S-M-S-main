<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\Student;
use App\Models\Teacher;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExerciseController extends Controller
{
    
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'date' => 'required|date',
        'study_stage_id' => 'required|exists:study_stages,id'
    ]);

    $user = JWTAuth::parseToken()->authenticate();

    $teacher = Teacher::where('user_id', $user->id)->first();

    if (!$teacher) {
        return response()->json([
            'message' => 'Only teachers can create exercises'
        ], 403);
    }

    $exercise = Exercise::create([
        'title' => $validated['title'],
        'content' => $validated['content'],
        'date' => $validated['date'],
        'study_stage_id' => $validated['study_stage_id'],
        'teacher_id' => $teacher->id,
    ]);

    return response()->json([
        'message' => 'Exercise created successfully',
        'data' => $exercise
    ], 201);
}
    
  public function getTeacherExercises()
{
    $user = JWTAuth::parseToken()->authenticate();

    $teacher = Teacher::where('user_id', $user->id)->first();

    if (!$teacher) {
        return response()->json([
            'message' => 'Only teachers can access'
        ], 403);
    }

    $exercises = Exercise::where('teacher_id', $teacher->id)
        ->with('stage') // جلب بيانات المرحلة الدراسية
        ->latest('date')
        ->get()
        ->map(function ($exercise) {
            return [
                'id'          => $exercise->id,
                'title'       => $exercise->title,
                'content'     => $exercise->content,
                'date'        => $exercise->date->format('Y-m-d'),
                'stage_number'=> $exercise->stage->stage_number ?? null,
            ];
        });

    return response()->json($exercises);
}



public function updateExercise(Request $request, $id)
{
    // مصادقة المستخدم
    $user = JWTAuth::parseToken()->authenticate();
    
    // جلب بيانات الأستاذ
    $teacher = Teacher::where('user_id', $user->id)->first();
    
    if (!$teacher) {
        return response()->json([
            'message' => 'Only teachers can update exercises'
        ], 403);
    }
    
    // جلب التمرين والتأكد أنه يخص هذا الأستاذ
    $exercise = Exercise::where('id', $id)
        ->where('teacher_id', $teacher->id)
        ->first();
    
    if (!$exercise) {
        return response()->json([
            'message' => 'Exercise not found or you are not authorized'
        ], 404);
    }
    
    // التحقق من صحة البيانات
    $validated = $request->validate([
        'title' => 'sometimes|required|string|max:255',
        'content' => 'sometimes|required|string',
        'date' => 'sometimes|required|date',
        'study_stage_id' => 'sometimes|required|exists:study_stages,id'
    ]);
    
    // تحديث التمرين
    $exercise->update($validated);
    
    return response()->json([
        'message' => 'Exercise updated successfully',
        'data' => $exercise
    ], 200);
}
public function deleteExercise($id)
{
    // مصادقة المستخدم
    $user = JWTAuth::parseToken()->authenticate();
    
    // جلب بيانات الأستاذ
    $teacher = Teacher::where('user_id', $user->id)->first();
    
    if (!$teacher) {
        return response()->json([
            'message' => 'Only teachers can delete exercises'
        ], 403);
    }
    
    // جلب التمرين والتأكد أنه يخص هذا الأستاذ
    $exercise = Exercise::where('id', $id)
        ->where('teacher_id', $teacher->id)
        ->first();
    
    if (!$exercise) {
        return response()->json([
            'message' => 'Exercise not found or you are not authorized'
        ], 404);
    }
    
    // حذف التمرين
    $exercise->delete();
    
    return response()->json([
        'message' => 'Exercise deleted successfully'
    ], 200);
}

public function getStudentExercises()
{
    $user = JWTAuth::parseToken()->authenticate();

    $student = Student::where('user_id', $user->id)->first();

    if (!$student || !$student->section) {
        return response()->json([
            'message' => 'Student not found or no section assigned'
        ], 404);
    }

    $stageId = $student->section->study_stage_id;

    $exercises = Exercise::where('study_stage_id', $stageId)
        ->with('teacher.user') // اسم الأستاذ الذي رفع التمرين
        ->latest('date')
        ->get()
        ->map(function ($exercise) {
            return [
                'id'           => $exercise->id,
                'title'        => $exercise->title,
                'content'      => $exercise->content,
                'date'         => $exercise->date->format('Y-m-d'),
                'teacher_name' => $exercise->teacher->user->name ?? 'Unknown',
            ];
        });

    return response()->json($exercises);
}
}