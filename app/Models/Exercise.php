<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
   protected $fillable = [
    'title',
    'content',
    'teacher_id',
    'student_id',
    'date',
    'study_stage_id',
];

    protected $casts = [
        'date' => 'date'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function stage()
{
    return $this->belongsTo(StudyStage::class, 'study_stage_id');
}
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}