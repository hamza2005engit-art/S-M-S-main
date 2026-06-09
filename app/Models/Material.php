<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory ;

    protected $fillable = [
        'name',
        'study_stage_id',
    ];

    public function studyStage()
    {
        return $this->belongsTo(StudyStage::class);
    }

    public function teacher()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_materials');
    }
}
