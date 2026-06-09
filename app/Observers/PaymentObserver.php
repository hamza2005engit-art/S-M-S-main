<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {

        $invoiceItem = $payment->invoiceItem()->first();
        $invoiceItem->amount_paid = $invoiceItem->payments()->sum('amount');
        if($invoiceItem->amount_paid >= $invoiceItem->total_price){
            $invoiceItem->status = 'paid';
        } elseif ($invoiceItem->amount_paid > 0) {
            $invoiceItem->status = 'partial';
        } else {
            $invoiceItem->status = 'unpaid';
        }
        $invoiceItem->save();
    }
}
