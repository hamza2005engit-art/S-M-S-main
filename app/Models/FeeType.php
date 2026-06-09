<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'name',
        'default_amount',
    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
