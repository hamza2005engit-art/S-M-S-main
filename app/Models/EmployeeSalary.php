<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    // public function salary() {
    //     return $this->belongsTo(Salary::class);
    // }

    protected $fillable = [
        'employeeable_id',
        'employeeable_type',
        'salary',
        'date',
        'paid',
        'paid_at',
    ];
 public function employeeable()
    {
        return $this->morphTo();
    }

}
