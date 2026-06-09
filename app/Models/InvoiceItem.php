<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'fee_type_id',
        'quantity',
        'unit_price',
        'total_price',
        'amount_paid',
        'status',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'Invoice_item_id');
    }
}
