<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Symfony\Component\Translation\t;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade')->nullable();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'excused'])->default('present');
             $table->foreignId('taken_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['student_id', 'section_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
