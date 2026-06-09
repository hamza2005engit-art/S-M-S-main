<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    
    public function createSection(Request $request)
    {
        $validatedData = $request->validate([
            'section_number' => 'required|integer|min:1|max:3',
            'study_stage_id' => 'required|exists:study_stages,id',
        ]);

        $section = Section::create($validatedData);
        $section->save();
        return response()->json(['section' => $section], 201);
    }
    public function getSection($id = null)
    {
        if ($id) {
            $section = Section::find($id);
            if (!$section) {
                return response()->json(['message' => 'Section not found'], 404);
            }
            return response()->json(['section' => $section], 200);
        } else {
            $sections = Section::all();
            return response()->json(['sections' => $sections], 200);
        }
    }
    public function updateSection(Request $request, $id)
    {
        $validatedData = $request->validate([
            'section_number' => 'required|integer|min:1|max:3',
            'study_stage_id' => 'required|exists:study_stages,id',
        ]);

        $section = Section::find($id);
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        $section->update($validatedData);
        $section->save();
        return response()->json(['section' => $section], 200);
    }
    public function deleteSection($id)
    {
        $section = Section::find($id);
        if (!$section) {
            return response()->json(['message' => 'Section not found'], 404);
        }

        $section->delete();

        return response()->json(['section' => $section], 200);
    }


}
