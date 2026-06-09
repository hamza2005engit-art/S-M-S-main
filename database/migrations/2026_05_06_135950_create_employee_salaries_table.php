<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->morphs('employeeable');
            $table->integer('salary')->nullable();
            $table->date('date')->nullable();
            $table->boolean('paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->unique([
                'employeeable_id',
                'employeeable_type',
                'date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};
