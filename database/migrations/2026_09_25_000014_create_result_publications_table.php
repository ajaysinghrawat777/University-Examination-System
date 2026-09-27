<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('result_publications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('version');
            $t->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('checksum', 64)->nullable();
            $t->timestampTz('published_at');
            $t->timestampsTz();
            $t->unique(['examination_id', 'version']);
            $t->index(['examination_id', 'published_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('result_publications');
    }
};
