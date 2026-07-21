<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'salary',
    ];
    public function salaries()
    {
        return $this->morphMany(EmployeeSalary::class, 'employeeable');
    }

    public function user(){
        return $this->belongsTo(User::class , 'user_id') ;
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'teacher_section');
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'teacher_materials');
    }
}
