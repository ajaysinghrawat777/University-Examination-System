<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mark_imports', function (Blueprint $t) {
            $t->id();
            $t->uuid('uuid')->unique();
            $t->foreignId('examination_id')->constrained()->cascadeOnDelete();
            $t->string('idempotency_key', 128);
            $t->string('file_path');
            $t->string('file_sha256', 64)->nullable();
            $t->string('status', 30)->default('queued');
            $t->unsignedBigInteger('total_rows')->default(0);
            $t->unsignedBigInteger('processed_rows')->default(0);
            $t->unsignedBigInteger('failed_rows')->default(0);
            $t->timestampTz('started_at')->nullable();
            $t->timestampTz('completed_at')->nullable();
            $t->text('failure_reason')->nullable();
            $t->timestampsTz();
            $t->unique(['examination_id', 'idempotency_key']);
            $t->index(['examination_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mark_imports');
    }
};
