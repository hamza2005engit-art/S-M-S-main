<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_stage_id',
        'section_id',
        'academic_year',
        'semester',
    ];

    public function studyStage()
    {
        return $this->belongsTo(StudyStage::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function slots()
    {
        return $this->hasMany(ScheduleSlot::class);
    }
}
