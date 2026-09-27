<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->foreignId('programme_id')->nullable()->constrained('programmes')->nullOnDelete();
            $t->string('admission_no', 64)->unique();
            $t->string('name');
            $t->string('email')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestampsTz();
            $t->index(['programme_id', 'is_active']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
