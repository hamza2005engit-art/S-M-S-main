<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyStage extends Model
{
    use HasFactory,SoftDeletes ;

    protected $fillable = [
     'stage_number',
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function exercises()
{
    return $this->hasMany(Exercise::class);
}

}
