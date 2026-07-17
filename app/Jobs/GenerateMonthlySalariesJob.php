<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\EmployeeSalary;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class GenerateMonthlySalariesJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(): void
{
    $today = Carbon::today();

    // Generate salaries for admins
    foreach (Admin::all() as $admin) {

        EmployeeSalary::updateOrCreate(
            [
                'employeeable_id'   => $admin->id,
                'employeeable_type' => 'admin', // أو Admin::class إذا لم تستخدم morphMap
                'date'              => $today,
            ],
            [
                'salary' => $admin->salary,
                'paid'   => false,
            ]
        );
    }

    // Generate salaries for teachers
    foreach (Teacher::all() as $teacher) {

        EmployeeSalary::updateOrCreate(
            [
                'employeeable_id'   => $teacher->id,
                'employeeable_type' => 'teacher', // أو Teacher::class إذا لم تستخدم morphMap
                'date'              => $today,
            ],
            [
                'salary' => $teacher->salary,
                'paid'   => false,
            ]
        );
    }
}}
