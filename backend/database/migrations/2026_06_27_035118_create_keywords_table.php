<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('keyword');
            $table->timestamps();

            // Index untuk mempercepat pencarian keyword
            $table->index(['entity_id']);
            $table->index(['keyword']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};