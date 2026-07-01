<?php

namespace App\Services\Reports\Sections;

use App\Models\SalaryPayment;
use Carbon\Carbon;

class SalaryReportSection
{
    public function get(
        Carbon $start,
        Carbon $end
    ): array {

        $totalSalary = SalaryPayment::query()
            ->whereBetween('payment_date', [$start, $end])
            ->sum('amount');

        $paymentsCount = SalaryPayment::query()
            ->whereBetween('payment_date', [$start, $end])
            ->count();

        return [
            'total_salary' => $totalSalary,
            'payments_count' => $paymentsCount,
        ];
    }
}
