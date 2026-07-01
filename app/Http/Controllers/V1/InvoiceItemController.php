<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    public function addInvoiceItem(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
        ]);
        $quantity = $data['quantity'] ?? 1;
        $unitPrice = $data['unit_price'] ?? FeeType::find($data['fee_type_id'])->default_amount;
        $invoiceItemData = [
            'invoice_id' => $data['invoice_id'],
            'fee_type_id' => $data['fee_type_id'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'amount_paid' => 0,
            'status' => 'unpaid',
        ];
        $invoiceItem = InvoiceItem::create($invoiceItemData);
        $invoiceItem->save();
        return response()->json(['message' => 'Invoice item added successfully', 'invoice_item' => $invoiceItem], 201);
    }

    public function getInvoiceItems($invoice_id)
    {
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice_id)->with('feeType:id,name')->get();
        return response()->json(['invoice_items' => $invoiceItems]);
    }

    public function updateInvoiceItem($id, Request $request)
    {
        $data = $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|in:unpaid,paid,partial',
        ]);

        $invoiceItem = InvoiceItem::findOrFail($id);
        if (!$invoiceItem) {
            return response()->json(['message' => 'Invoice item not found'], 404);
        }
        $invoiceItem->update($data);
        $invoiceItem->total_price = $invoiceItem->quantity * $invoiceItem->unit_price;
        if($invoiceItem->amount_paid >= $invoiceItem->total_price){
            $invoiceItem->status = 'paid';
        } elseif ($invoiceItem->amount_paid > 0) {
            $invoiceItem->status = 'partial';
        } else {
            $invoiceItem->status = 'unpaid';
        }
        $invoiceItem->save();
        return response()->json(['message' => 'Invoice item updated successfully', 'invoice_item' => $invoiceItem], 200);
    }

    public function deleteInvoiceItem($id)
    {
        $invoiceItem = InvoiceItem::findOrFail($id);
        if (!$invoiceItem) {
            return response()->json(['message' => 'Invoice item not found'], 404);
        }
        $invoiceItem->delete();
        return response()->json(['message' => 'Invoice item deleted successfully'], 200);
    }
}
