<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mark_import_errors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('mark_import_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('row_number');
            $t->jsonb('payload');
            $t->text('error');
            $t->timestampsTz();
            $t->unique(['mark_import_id', 'row_number']);
            $t->index(['mark_import_id', 'row_number']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mark_import_errors');
    }
};
