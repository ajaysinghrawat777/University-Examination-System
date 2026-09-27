<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('examination_course_assessment_id')->constrained()->cascadeOnDelete();
            $t->decimal('marks', 8, 2);
            $t->foreignId('mark_import_id')->nullable()->constrained()->nullOnDelete();
            $t->string('source_fingerprint', 64)->nullable();
            $t->timestampsTz();
            $t->unique(['student_id', 'examination_course_assessment_id']);
            $t->index(['examination_course_assessment_id', 'student_id']);
            $t->index(['mark_import_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
