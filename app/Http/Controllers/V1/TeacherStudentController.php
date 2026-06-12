<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherStudentController extends Controller
{
    public function index(Request $request)
    {
        // 1. جلب الأستاذ الحالي
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();

        // 2. جلب الصفوف التابعة له
        $sectionIds = $teacher->sections()->pluck('sections.id');

        // 3. بناء الاستعلام
        $query = Student::with(['user', 'section'])
            ->whereIn('section_id', $sectionIds);

        // 4. فلترة حسب الصف (اختياري)
        if ($request->filled('section_id')) {

            // تأكد الصف تابع للأستاذ
            if (!$sectionIds->contains($request->section_id)) {
                return response()->json([
                    'message' => 'هذا الصف غير تابع لك'
                ], 403);
            }

            $query->where('section_id', $request->section_id);
        }

        return response()->json([
            'students' => $query->get()
        ]);
    }
}