<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('original_path')->comment('Original uploaded image path');
            $table->string('thumbnail_path')->comment('Optimized WEBP thumbnail path');
            $table->string('hash', 64)->nullable()->comment('SHA-256 hash for sync');
            $table->unsignedInteger('width')->nullable()->comment('Thumbnail width in pixels');
            $table->unsignedInteger('height')->nullable()->comment('Thumbnail height in pixels');
            $table->unsignedBigInteger('size')->nullable()->comment('Thumbnail file size in bytes');
            $table->timestamps();

            // Satu entity hanya boleh punya satu image
            $table->unique(['entity_id']);

            // Index untuk sync
            $table->index(['hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};