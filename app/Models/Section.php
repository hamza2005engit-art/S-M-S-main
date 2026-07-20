<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_number',
        'study_stage_id',
    ];

    public function studyStage()
    {
        return $this->belongsTo(StudyStage::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_sections');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
