<?php

namespace App\Models;

use App\Models\Mark;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'user_id',
        'section_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}
