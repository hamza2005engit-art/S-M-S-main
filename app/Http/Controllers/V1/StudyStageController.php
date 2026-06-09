<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StageSectionResource;
use App\Models\StudyStage;
use Illuminate\Http\Request;

class StudyStageController extends Controller
{

    public function createStudyStage(Request $request)
    {
        $request->validate([
            'stage_number' => 'required|integer|min:1|max:12|unique:study_stages,stage_number',
        ]);

        $studyStage = StudyStage::create([
            'stage_number' => $request->stage_number,
        ]);
        $studyStage->save();

        return response()->json( ['data' => $studyStage], 201);
    }

    public function getStudyStage($id = null)
    {
        if ($id) {
            $studyStage = StudyStage::find($id);
            if (!$studyStage) {
                return response()->json(['message' => 'Study stage not found'], 404);
            }
            return response()->json([new StageSectionResource($studyStage)], 200);
        } else {
            $studyStages = StudyStage::all();
            return StageSectionResource::collection($studyStages);
        }
    }

    public function updateStudyStage(Request $request, $id)
    {
        $request->validate([
            'stage_number' => 'required|integer|min:1|max:12|unique:study_stages,stage_number',
        ]);

        $studyStage = StudyStage::find($id);
        if (!$studyStage) {
            return response()->json(['message' => 'Study stage not found'], 404);
        }

        $studyStage->update([
            'stage_number' => $request->stage_number,
        ]);
        $studyStage->save();

        return response()->json(['message' => 'Study stage updated successfully', 'data' => $studyStage], 200);
    }

    public function deleteStudyStage($id)
    {
        $studyStage = StudyStage::find($id);
        if (!$studyStage) {
            return response()->json(['message' => 'Study stage not found'], 404);
        }

        $studyStage->delete();

        return response()->json(['message' => 'Study stage deleted successfully'], 200);
    }
}
