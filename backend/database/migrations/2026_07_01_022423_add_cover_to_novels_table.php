<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('novels', function (Blueprint $table) {
            $table->string('cover_path')->nullable()->after('hash');
            $table->string('cover_thumbnail_path')->nullable()->after('cover_path');
        });
    }

    public function down(): void
    {
        Schema::table('novels', function (Blueprint $table) {
            $table->dropColumn(['cover_path', 'cover_thumbnail_path']);
        });
    }
};