<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSlot extends Model
{
    use HasFactory ;

    protected $fillable = [
        'schedule_id',
        'day_of_week',
       'period_id',
        'material_id',
        'course_id',
        'teacher_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class,'material_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
