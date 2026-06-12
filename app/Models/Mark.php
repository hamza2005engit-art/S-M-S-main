<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
       protected $fillable = [
        'student_id',
        'teacher_id',
        'material_id',
        'type',
        'score',
    ];

    public function student()
{
    return $this->belongsTo(Student::class);
}

public function material()
{
    return $this->belongsTo(Material::class);
}

public function teacher()
{
    
    return $this->belongsTo(Teacher::class);
}
}
