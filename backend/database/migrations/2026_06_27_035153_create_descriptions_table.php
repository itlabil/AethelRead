<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->enum('locale', ['en', 'id'])->default('en');
            $table->text('content');
            $table->timestamps();

            // Satu entity hanya boleh punya satu description per locale
            $table->unique(['entity_id', 'locale']);

            // Index untuk mempercepat pencarian per locale
            $table->index(['entity_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descriptions');
    }
};