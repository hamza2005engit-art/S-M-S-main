<?php

namespace App\Services\V1;

use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SetStudentInSectionService
{
    public function setStudentInSection(Student $student, Section $section): Student
    {
        DB::transaction(function () use ($section) {

            $count = Student::where('section_id', $section->id)
                ->lockForUpdate()
                ->count();

            if ($count >= 20) {
                throw ValidationException::withMessages([
                    'section_id' => 'Section is full'
                ]);
            }
        });

        $student->section_id = $section->id;
        $student->save();

        return $student;
    }
}
