<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('examination_courses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_id')->constrained()->restrictOnDelete();
            $t->timestampsTz();
            $t->unique(['examination_id', 'course_id']);
            $t->index(['examination_id', 'course_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('examination_courses');
    }
};
