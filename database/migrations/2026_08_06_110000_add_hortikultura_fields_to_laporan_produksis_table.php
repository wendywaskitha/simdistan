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
        Schema::table('komoditas', function (Blueprint $table) {
            $table->enum('jenis_periode', ['Bulanan', 'Triwulanan'])->default('Bulanan')->after('nama');
            $table->enum('form_type', ['SPH-SBS', 'SPH-BST', 'SPH-TBF'])->nullable()->after('jenis_periode');
        });

        Schema::table('laporan_produksis', function (Blueprint $table) {
            $table->decimal('luas_rusak', 12, 2)->default(0.00)->after('luas_panen');
            $table->bigInteger('jumlah_tanaman_menghasilkan')->default(0)->after('luas_rusak');
            $table->string('jenis_periode', 20)->nullable()->after('tahun');
            $table->string('form_type', 20)->nullable()->after('jenis_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komoditas', function (Blueprint $table) {
            $table->dropColumn(['jenis_periode', 'form_type']);
        });

        Schema::table('laporan_produksis', function (Blueprint $table) {
            $table->dropColumn(['luas_rusak', 'jumlah_tanaman_menghasilkan', 'jenis_periode', 'form_type']);
        });
    }
};
