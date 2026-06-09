<?php

namespace App\Services\V1;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\FeeType;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateInvoiceTutionService
{
    public function createTuitionInvoice(Student $student)
    {
        $tution_fee = FeeType::where('name', 'Tuition')->FirstOrFail();

        $invoice = Invoice::create([
            'student_id' => $student->id,
            'due_date' => Carbon::now(),
        ]);
        $invoice->save();

        $invoice_item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'fee_type_id' => $tution_fee->id,
            'quantity' => 1,
            'unit_price' => $tution_fee->default_amount,
            'total_price' => $tution_fee->default_amount,
        ]);
        $invoice_item->save();

        $total = $invoice->invoiceItems()->sum('total_price');

        $invoice->update([
            'total_amount' => $total,
        ]);
        $invoice->save();

        return $invoice->with('invoiceItems');

    }
}
