<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class TeacherController extends Controller

{

    public function assignTeacherToSection(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        if (!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }


        $section = Section::findOrFail($request->section_id);
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        if ($teacher->sections()->where('section_id', $request->section_id)->exists()) {
            return response()->json(['message' => 'Teacher is already assigned to this section'], 400);
        }

        $teacher->sections()->attach($section);
        $teacher->save();
        return response()->json(['message' => 'Teacher assigned to section successfully'], 200);
    }

    public function assignMaterialToTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'material_id' => 'required|exists:materials,id',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        if(!$teacher) {
            return response()->json(['message' => 'Teacher not found'], 404);
        }
        $material = Material::findOrFail($request->material_id);
        if(!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        if($teacher->materials()->where('material_id', $request->material_id)->exists()) {
            return response()->json(['message' => 'Material is already assigned to this teacher'], 400);
        }

        $teacher->materials()->attach($material);
        $teacher->save();
        return response()->json(['message' => 'Material assigned to teacher successfully'], 200);
    }
}
