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
        Schema::table('laporan_produksis', function (Blueprint $table) {
            $table->decimal('luas_lahan', 12, 2)->default(0.00)->after('luas_panen');
        });

        Schema::table('laporan_produksi_mingguans', function (Blueprint $table) {
            $table->decimal('luas_lahan', 12, 2)->default(0.00)->after('luas_panen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_produksis', function (Blueprint $table) {
            $table->dropColumn('luas_lahan');
        });

        Schema::table('laporan_produksi_mingguans', function (Blueprint $table) {
            $table->dropColumn('luas_lahan');
        });
    }
};
