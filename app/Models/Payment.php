<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'Invoice_item_id',
        'amount',
        'receipt_number',
        'payment_at',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class, 'Invoice_item_id');
    }
}
