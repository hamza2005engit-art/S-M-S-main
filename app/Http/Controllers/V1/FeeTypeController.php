<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function createFee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'default_amount' => 'required|numeric|min:0',
        ]);

        $feeType = FeeType::create([
            'name' => $request->name,
            'default_amount' => $request->default_amount,
        ]);
        $feeType->save();

        return response()->json(['data' => $feeType], 201);
    }

    public function getFee($id = null)
    {
        if ($id) {
            $feeType = FeeType::findOrFail($id);
            if(!$feeType){
                return response()->json(['message' => 'Fee type not found'], 404);
            }
        } else {
            $feeType = FeeType::paginate(10);
        }
        return response()->json(['data' => $feeType], 200);
    }

    public function updateFee($id, Request $request)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'default_amount' => 'sometimes|required|numeric|min:0',
        ]);

        $feeType = FeeType::findOrFail($id);
        if(!$feeType){
            return response()->json(['message' => 'Fee type not found'], 404);
        }
        $feeType->update($request->only(['name', 'default_amount']));
        $feeType->save();
        return response()->json(['message' => 'Fee type updated successfully', 'data' => $feeType], 200);
    }

    public function deleteFee($id)
    {
        $feeType = FeeType::findOrFail($id);
        if(!$feeType){
            return response()->json(['message' => 'Fee type not found'], 404);
        }
        $feeType->delete();

        return response()->json(['message' => 'Fee type deleted successfully'], 200);
    }
}
