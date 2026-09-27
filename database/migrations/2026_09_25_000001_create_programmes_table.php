<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $t) {
            $t->id();
            $t->string('code', 40)->unique();
            $t->string('name');
            $t->boolean('is_active')->default(true);
            $t->timestampsTz();
            $t->index(['is_active', 'code']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
