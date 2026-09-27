<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_results', function (Blueprint $t) {
            $t->id();
            $t->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->restrictOnDelete();
            $t->decimal('total_marks', 10, 2)->default(0);
            $t->decimal('max_marks', 10, 2)->default(0);
            $t->decimal('percentage', 8, 4)->default(0);
            $t->string('grade', 10);
            $t->string('status', 20);
            $t->timestampTz('calculated_at');
            $t->timestampsTz();
            $t->unique(['examination_id', 'student_id', 'course_id']);
            $t->index(['examination_id', 'student_id']);
            $t->index(['examination_id', 'course_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('course_results');
    }
};
