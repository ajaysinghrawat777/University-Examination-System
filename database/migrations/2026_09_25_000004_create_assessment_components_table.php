<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessment_components', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->string('code', 40);
            $t->string('name');
            $t->timestampsTz();
            $t->unique(['course_id', 'code']);
            $t->index(['course_id', 'code']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('assessment_components');
    }
};
