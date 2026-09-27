<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('examinations', function (Blueprint $t) {
            $t->id();
            $t->string('code', 60)->unique();
            $t->string('name');
            $t->string('academic_year', 20);
            $t->string('term', 30);
            $t->string('status', 20)->default('draft');
            $t->timestampTz('published_at')->nullable();
            $t->timestampsTz();
            $t->index(['academic_year', 'term', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
