<?php

namespace App\Services\V1;

use App\Models\Student;

class StudentNumberService
{
    public static function generate(): string
    {
        $year = now()->year;

        $lastStudent = Student::where('student_number', 'like', $year . '%')
            ->latest('id')
            ->first();

        $sequence = $lastStudent
            ? ((int) substr($lastStudent->student_number, -3)) + 1
            : 1;

        return $year . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}

