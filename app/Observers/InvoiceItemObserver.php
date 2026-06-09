<?php

namespace App\Observers;

use App\Models\InvoiceItem;

class InvoiceItemObserver
{
    public function created(InvoiceItem $invoiceItem): void
    {
        $invoice = $invoiceItem->invoice()->first();
        $invoice->update([
            'total_amount' => $invoice->invoiceItems()->sum('total_price'),
        ]);
        $invoice->save();
    }

    public function updated(InvoiceItem $invoiceItem): void
    {
        $invoice = $invoiceItem->invoice()->first();
        
        $invoice->update([
            'total_amount' => $invoice->invoiceItems()->sum('total_price'),
        ]);
        $invoice->save();
    }
}
