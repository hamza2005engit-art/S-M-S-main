<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'total_amount' => 'nullable|numeric',
            'due_date' => 'required|date|date_format:Y-m-d',
        ]);

        $invoiceData = [
            'student_id' => $data['student_id'],
            'total_amount' => $data['total_amount']??0,
            'total_paid' => 0,
            'due_date' => $data['due_date'],
            'status' => 'unpaid',
        ];
        $invoice = Invoice::create($invoiceData);
        $invoice->save();
        return response()->json(['message' => 'Invoice created successfully', 'data' => $invoice], 201);
    }

    public function getInvoice($id = null)
    {
        if ($id) {
            $invoice = Invoice::findOrFail($id);
            if (!$invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }
            return response()->json(['data' => $invoice], 200);
        } else {
            $invoice = Invoice::paginate(10);
        }
        return InvoiceResource::collection($invoice);
    }

    public function updateInvoice($id, Request $request)
    {
        $data = $request->validate([
            'total_amount' => 'sometimes|numeric',
            'due_date' => 'sometimes|date|date_format:Y-m-d',
            'status' => 'sometimes|in:unpaid,paid,partial,overdue',
        ]);

        $invoice = Invoice::findOrFail($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoice->update($data);
        $invoice->save();
        return response()->json(['message' => 'Invoice updated successfully', 'data' => $invoice], 200);
    }

    public function deleteInvoice($id)
    {
        $invoice = Invoice::findOrFail($id);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }
}
