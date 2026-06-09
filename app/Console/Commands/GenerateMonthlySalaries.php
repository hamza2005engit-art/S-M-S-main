<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMonthlyAdminSalariesJob;
use App\Jobs\GenerateMonthlySalariesJob;
use App\Models\Admin;
use App\Models\EmployeeSalary;
use App\Models\Teacher;
use Illuminate\Console\Command;

class GenerateMonthlySalaries extends Command
{
    protected $signature = 'salary:generate';

    protected $description = 'Generate monthly salaries for employees';

    public function handle()
    {

        GenerateMonthlySalariesJob::dispatch();

        $this->info('Salary generation dispatched.');
    
    }
}
