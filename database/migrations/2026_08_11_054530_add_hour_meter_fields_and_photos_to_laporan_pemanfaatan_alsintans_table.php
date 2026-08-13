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
            $table->decimal('hour_meter', 10, 2)->nullable()->change();
            $table->decimal('hour_meter_awal', 10, 2)->nullable()->after('biaya_pengolahan');
            $table->decimal('hour_meter_akhir', 10, 2)->nullable()->after('hour_meter_awal');
            $table->string('foto_hm_awal')->nullable()->after('hour_meter_akhir');
            $table->string('foto_hm_akhir')->nullable()->after('foto_hm_awal');
        });

        // Copy existing hour_meter values to hour_meter_awal and hour_meter_akhir
        \Illuminate\Support\Facades\DB::table('laporan_pemanfaatan_alsintans')->update([
            'hour_meter_awal' => \Illuminate\Support\Facades\DB::raw('hour_meter'),
            'hour_meter_akhir' => \Illuminate\Support\Facades\DB::raw('hour_meter'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_pemanfaatan_alsintans', function (Blueprint $table) {
            $table->decimal('hour_meter', 10, 2)->nullable(false)->change();
            $table->dropColumn(['hour_meter_awal', 'hour_meter_akhir', 'foto_hm_awal', 'foto_hm_akhir']);
        });
    }
};
