<?php

namespace App\Services\Reports\Sections;

use App\Models\Invoice;
use Carbon\Carbon;

class FinancialReportSection
{
    public function get(
        Carbon $start,
        Carbon $end
    ): array {

        $issuedInvoices = Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $paidInvoices = Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->count();

        $pendingInvoices = Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'pending')
            ->count();

        $totalRevenue = Invoice::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'paid')
            ->sum('amount');

        return [
            'issued_invoices' => $issuedInvoices,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $pendingInvoices,
            'total_revenue' => $totalRevenue,
        ];
    }
}
