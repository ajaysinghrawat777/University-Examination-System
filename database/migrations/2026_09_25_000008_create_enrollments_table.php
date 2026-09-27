<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->string('status', 20)->default('active');
            $t->timestampsTz();
            $t->unique(['examination_id', 'student_id']);
            $t->index(['examination_id', 'status', 'student_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
