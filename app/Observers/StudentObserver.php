<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\User;
use App\Services\V1\CreateInvoiceTutionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StudentObserver
{
    // public function saving(Student $student): void
    // {
    //     if (! $student->section_id) {
    //         return;
    //     }

    //     $sectionStudentCount = Student::where('section_id', $student->section_id)
    //         ->when($student->exists, fn ($query) => $query->where('id', '!=', $student->id))
    //         ->count();

    //     if ($sectionStudentCount >= 20) {
    //         throw ValidationException::withMessages([
    //             'section_id' => ['This section cannot have more than 20 students.'],
    //         ]);
    //     }

//         DB::transaction(function () {

//     $count = Student::where('section_id', $sectionId)
//         ->lockForUpdate()
//         ->count();

//     if ($count >= 20) {
//         throw ValidationException::withMessages([
//             'section_id' => 'Section is full'
//         ]);
//     }

//     Student::create($data);
// });
//     }

    public function created(Student $student): void
    {
        $user = $student->user()->first();

        if ($user && $user->approved) {
            app(CreateInvoiceTutionService::class)
                ->createTuitionInvoice($student);
        }
    }
}
