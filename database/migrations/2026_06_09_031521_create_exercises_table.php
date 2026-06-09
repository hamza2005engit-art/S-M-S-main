<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
 Schema::create('exercises', function (Blueprint $table) {
    $table->id();

    $table->string('title');
    $table->text('content');

    $table->foreignId('teacher_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('study_stage_id')
        ->constrained('study_stages')
        ->cascadeOnDelete();

    $table->date('date');

    $table->timestamps();
});
}

 public function down(): void
{
    Schema::dropIfExists('exercises');
}
};