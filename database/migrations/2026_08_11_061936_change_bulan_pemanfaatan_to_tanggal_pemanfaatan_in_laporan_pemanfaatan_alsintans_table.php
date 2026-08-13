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
        Schema::table('laporan_pemanfaatan_alsintans', function (Blueprint $table) {
            $table->dropColumn(['bulan_mulai', 'bulan_selesai']);
            $table->date('tanggal_mulai')->nullable()->after('foto_dokumentasi');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_pemanfaatan_alsintans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
            $table->string('bulan_mulai')->nullable()->after('foto_dokumentasi');
            $table->string('bulan_selesai')->nullable()->after('bulan_mulai');
        });
    }
};
