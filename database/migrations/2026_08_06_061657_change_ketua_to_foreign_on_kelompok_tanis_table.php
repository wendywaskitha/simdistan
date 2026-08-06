<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelompok_tanis', function (Blueprint $table) {
            $table->unsignedBigInteger('ketua_petani_id')->nullable()->after('gapoktan_id');
            $table->foreign('ketua_petani_id')->references('id')->on('petanis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelompok_tanis', function (Blueprint $table) {
            $table->dropForeign(['ketua_petani_id']);
            $table->dropColumn('ketua_petani_id');
        });
    }
};
