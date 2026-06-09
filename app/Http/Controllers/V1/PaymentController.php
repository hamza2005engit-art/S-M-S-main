<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payInvoice($invoideId,$itemId, Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'payment_at' => 'sometimes|date|date_format:Y-m-d',
        ]);

        $invoice = Invoice::findOrFail($invoideId);
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice is already paid'], 400);
        }
            $invoiceItem = $invoice->invoiceItems()->find($itemId);
        if (!$invoiceItem) {
            return response()->json(['message' => 'Invoice item not found'], 404);
        }
        do {

            $receiptNumber = fake()
                ->numerify('##########');
        } while (
            Payment::where('receipt_number', $receiptNumber)->exists()
        );
        $data = [
            'invoice_id' => $invoice->id,
            'Invoice_item_id' => $invoiceItem->id,
            'amount' => $request->amount,
            'receipt_number' => $receiptNumber,
            'payment_at' => $request->payment_at ?? now(),
        ];
        $payment = new Payment($data);
        $payment->save();

        if ($invoice->total_amount <= $invoice->payments()->sum('amount')) {
            $invoice->total_paid = $invoice->payments()->sum('amount');
            $invoice->status = 'paid';
            $invoice->save();
        }
        if ($invoice->total_amount > $invoice->payments()->sum('amount') && $invoice->payments()->sum('amount') > 0) {
            $invoice->total_paid = $invoice->payments()->sum('amount');
            $invoice->status = 'partial';
            $invoice->save();
        }

        return response()->json(['message' => 'Payment successful', 'payment' => $payment], 201);
    }

    // public function paymentHistory()
    // {
    //     $payments = Payment::with('invoice')->orderBy('payment_at', 'desc')->paginate(5);

    //     return response()->json(['payments' => $payments], 200);
    // }
}
