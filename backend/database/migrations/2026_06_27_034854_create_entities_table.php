<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('novel_id')->constrained('novels')->cascadeOnDelete();
            $table->enum('type', [
                'character',
                'place',
                'item',
            ])->default('character');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hash', 64)->nullable()->comment('SHA-256 hash for sync');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index untuk mempercepat pencarian per novel
            $table->index(['novel_id', 'type']);
            $table->index(['novel_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};