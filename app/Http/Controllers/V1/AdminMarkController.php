<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Mark;
use Illuminate\Http\Request;

class AdminMarkController extends Controller
{
    /**
     * تحقق أن المستخدم الحالي أدمن
     */
    private function authorizeAdmin()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $admin = Admin::where('user_id', $user->id)->first();

        if (!$admin) {
            return response()->json(['message' => 'Only admins can access this resource'], 403);
        }

        return null;
    }

    /**
     * عرض كل العلامات (مع فلاتر اختيارية)
     */
    public function index(Request $request)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $marks = Mark::with(['student.user', 'teacher.user', 'material']);

        if ($request->filled('student_id')) {
            $marks->where('student_id', $request->student_id);
        }

        if ($request->filled('teacher_id')) {
            $marks->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('material_id')) {
            $marks->where('material_id', $request->material_id);
        }

        if ($request->filled('type')) {
            $marks->where('type', $request->type);
        }

        return response()->json([
            'marks' => $marks->latest()->get()
        ]);
    }

    /**
     * إضافة علامة لأي طالب مع أي أستاذ
     */
    public function store(Request $request)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'teacher_id'  => 'required|exists:teachers,id',
            'material_id' => 'required|exists:materials,id',
            'type'        => 'required|in:exercise,test,final',
            'score'       => 'required|integer|min:0|max:100',
        ]);

        $mark = Mark::create([
            'student_id'  => $request->student_id,
            'teacher_id'  => $request->teacher_id,
            'material_id' => $request->material_id,
            'type'        => $request->type,
            'score'       => $request->score,
        ]);

        return response()->json([
            'message' => 'Mark added successfully',
            'data' => $mark
        ], 201);
    }

    /**
     * تعديل علامة بأي ID
     */
    public function update(Request $request, $id)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $mark = Mark::find($id);

        if (!$mark) {
            return response()->json(['message' => 'Mark not found'], 404);
        }

        $request->validate([
            'student_id'  => 'sometimes|required|exists:students,id',
            'teacher_id'  => 'sometimes|required|exists:teachers,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'type'        => 'sometimes|required|in:exercise,test,final',
            'score'       => 'sometimes|required|integer|min:0|max:100',
        ]);

        $mark->update($request->only([
            'student_id', 'teacher_id', 'material_id', 'type', 'score'
        ]));

        return response()->json([
            'message' => 'Mark updated successfully',
            'data' => $mark
        ]);
    }

    /**
     * حذف علامة
     */
    public function destroy($id)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $mark = Mark::find($id);

        if (!$mark) {
            return response()->json(['message' => 'Mark not found'], 404);
        }

        $mark->delete();

        return response()->json(['message' => 'Mark deleted successfully']);
    }

    /**
     * تقرير علامات طالب معين (بدون اشتراط ربط بأستاذ معين)
     */
    public function studentReport($student_id)
    {
        if ($error = $this->authorizeAdmin()) return $error;

        $student = \App\Models\Student::with(['user', 'section.studyStage'])
            ->findOrFail($student_id);
 $marks = Mark::with(['material'])
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

            $result[] = [
                'material_id'   => $materialId,
                'material_name' => $items->first()?->material?->name,
                'exercise'      => $exercise,
                'test'          => $test,
                'final'         => $final,
                'total'         => $total,
                'average'       => round($total / 3, 2),
            ];

            $overallTotal += $total;
            $subjectCount++;
        }

        $finalAverage = $subjectCount > 0 ? $overallTotal / $subjectCount : 0;

        return response()->json([
            'student' => [
                'id'      => $student->id,
                'name'    => $student->user->full_name ?? $student->user->name,
                'stage'   => $student->section->studyStage->stage_number ?? null,
                'section' => $student->section->section_number ?? null,
            ],
            'subjects' => $result,
            'final_average' => round($finalAverage, 2),
        ]);
    }
}