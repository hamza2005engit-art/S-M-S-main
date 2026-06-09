<?php

namespace App\Jobs;

use App\Models\EmployeeSalary;
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
        $employees = User::role(['admin', 'teacher'])->with('roles')->get();

        foreach ($employees as $employee) {
            EmployeeSalary::updateOrCreate(
                [
                    'employeeable_id' => $employee->id,
                    'employeeable_type' => $employee->getRoleNames()->first(),
                    'date' => Carbon::now()->format('Y-m-d'),
                ],
                [
                    'amount' => $employee->salary,
                    'paid' => false,
                ]
            );
        }
    }
}
