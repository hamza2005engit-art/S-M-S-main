<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudyStage;
use App\Services\V1\SetStudentInSectionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function setStudentInSection(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        if ($student->section_id) {
            return response()->json(['message' => 'Student is already assigned to a section'], 400);
        }

        $section = Section::findOrFail($request->section_id);
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        app(SetStudentInSectionService::class)->setStudentInSection($student, $section);

        return response()->json(['data' => $student], 200);
    }
}
