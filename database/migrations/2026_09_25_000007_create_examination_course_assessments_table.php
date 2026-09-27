<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('examination_course_assessments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('examination_course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('assessment_component_id')->constrained()->restrictOnDelete();
            $t->decimal('max_marks', 8, 2);
            $t->decimal('pass_marks', 8, 2)->default(0);
            $t->decimal('weightage', 8, 4)->default(1);
            $t->boolean('is_mandatory')->default(true);
            $t->timestampsTz();
            $t->unique(['examination_course_id', 'assessment_component_id'], 'examination_course_id_assessment_component_id_unique');
            $t->index(['examination_course_id', 'assessment_component_id'], 'examination_course_id_assessment_component_id_index');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('examination_course_assessments');
    }
};
