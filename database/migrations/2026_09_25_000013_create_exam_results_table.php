<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $t) {
            $t->id();
            $t->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('courses_count')->default(0);
            $t->decimal('total_marks', 12, 2)->default(0);
            $t->decimal('max_marks', 12, 2)->default(0);
            $t->decimal('percentage', 8, 4)->default(0);
            $t->string('grade', 10);
            $t->string('status', 20);
            $t->timestampTz('calculated_at');
            $t->timestampTz('published_at')->nullable();
            $t->timestampsTz();
            $t->unique(['examination_id', 'student_id']);
            $t->index(['examination_id', 'status', 'student_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
