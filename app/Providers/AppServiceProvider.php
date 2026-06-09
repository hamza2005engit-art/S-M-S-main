<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Observers\InvoiceItemObserver;
use App\Observers\InvoiceObserver;
use App\Observers\PaymentObserver;
use App\Observers\StudentObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'admin' => Admin::class,
            'teacher' => Teacher::class,
            'App\Models\User' => User::class,
        ]);

        Student::observe(StudentObserver::class);
        Invoice::observe(InvoiceObserver::class);
        InvoiceItem::observe(InvoiceItemObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
