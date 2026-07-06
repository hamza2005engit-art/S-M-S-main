<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function createMaterial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'study_stage_id' => 'required|exists:study_stages,id',
        ]);

        $material = new Material();
        $material->name = $request->input('name');
        $material->study_stage_id = $request->input('study_stage_id');
        $material->save();

        return response()->json(['message' => 'Material created successfully', 'material' => $material], 201);
    }

    public function getMaterial($study_stage_id = null)
    {
        if ($study_stage_id) {
            $materials = Material::where('study_stage_id', $study_stage_id)->get();
        } else {
            $materials = Material::all();
        }

        return response()->json(['materials' => $materials], 200);
    }
}
