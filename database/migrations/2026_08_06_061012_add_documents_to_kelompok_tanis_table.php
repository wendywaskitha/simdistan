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
            $table->string('sk_pembentukan')->nullable()->after('ketua');
            $table->string('berita_acara')->nullable()->after('sk_pembentukan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelompok_tanis', function (Blueprint $table) {
            $table->dropColumn(['sk_pembentukan', 'berita_acara']);
        });
    }
};
