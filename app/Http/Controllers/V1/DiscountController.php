<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function createDiscount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:percentage,fixed',
        ]);

        $discount = Discount::create($request->all());
        $discount->save();
        return response()->json(['message' => 'Discount created successfully', 'discount' => $discount], 201);
    }

    public function getDiscount($id = null)
    {
        if ($id) {
            $discount = Discount::findOrFail($id);
            if (!$discount) {
                return response()->json(['message' => 'Discount not found'], 404);
            }
        } else {
            $discount = Discount::paginate(10);
        }
        return response()->json(['discounts' => $discount], 200);
    }

    public function updateDiscount($id, Request $request)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'type' => 'sometimes|required|in:percentage,fixed',
        ]);

        $discount = Discount::findOrFail($id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }
        $discount->update($request->all());
        $discount->save();
        return response()->json(['message' => 'Discount updated successfully', 'discount' => $discount], 200);
    }

    public function deleteDiscount($id)
    {
        $discount = Discount::findOrFail($id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }
        $discount->delete();
        return response()->json(['message' => 'Discount deleted successfully'], 200);
    }

    public function applyDiscount($invoice_id, Request $request)
    {
        $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric',
        ]);

        $discount = Discount::findOrFail($request->discount_id);
        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }
        $invoice = Invoice::findOrFail($request->invoice_id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        // Apply discount logic here (e.g., update invoice total)
        if ($discount->type === 'percentage') {
            $invoice->total_amount -= ($invoice->total_amount * ($discount->amount / 100));
        } else {
            $invoice->total_amount -= $discount->amount;
        }
        $invoice->save();

        return response()->json(['message' => 'Discount applied successfully', 'discount' => $discount], 200);
    }

}
