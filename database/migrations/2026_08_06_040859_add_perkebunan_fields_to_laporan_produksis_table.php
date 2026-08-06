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
            // SPH Perkebunan Rakyat (Tahunan/Semesteran)
            $table->decimal('luas_akhir_tahun_lalu', 10, 2)->nullable()->after('tanaman_tus_rusak');
            $table->decimal('tanam_ulang', 10, 2)->nullable()->after('luas_akhir_tahun_lalu');
            $table->decimal('tanam_baru', 10, 2)->nullable()->after('tanam_ulang');
            $table->decimal('pengurangan', 10, 2)->nullable()->after('tanam_baru');
            $table->decimal('luas_jumlah', 10, 2)->nullable()->after('pengurangan'); // 7 = 3+5-6 (mutasi)
            $table->decimal('tbm', 10, 2)->nullable()->after('luas_jumlah'); // Tanaman Belum Menghasilkan
            $table->decimal('tm', 10, 2)->nullable()->after('tbm');   // Tanaman Menghasilkan
            $table->decimal('ttm', 10, 2)->nullable()->after('tm');   // Tanaman Tua/Tidak Menghasilkan/Rusak
            $table->decimal('produksi_akhir_tahun_lalu', 10, 2)->nullable()->after('ttm');
            $table->string('wujud_produksi')->nullable()->after('produksi_akhir_tahun_lalu');
            $table->integer('jumlah_petani_pemilik')->nullable()->after('wujud_produksi');
            $table->integer('jumlah_petani_bmu')->nullable()->after('jumlah_petani_pemilik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_produksis', function (Blueprint $table) {
            $table->dropColumn([
                'luas_akhir_tahun_lalu',
                'tanam_ulang',
                'tanam_baru',
                'pengurangan',
                'luas_jumlah',
                'tbm',
                'tm',
                'ttm',
                'produksi_akhir_tahun_lalu',
                'wujud_produksi',
                'jumlah_petani_pemilik',
                'jumlah_petani_bmu'
            ]);
        });
    }
};
