<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom-kolom khusus SPH (SBS, BST, TBF) untuk form Hortikultura BPS.
     */
    public function up(): void
    {
        Schema::table('laporan_produksis', function (Blueprint $table) {
            // ── SPH-SBS & SPH-TBF: kolom berbasis luas (Ha / m²) ─────────────────
            // Luas Tanaman Akhir Bulan/Triwulan Lalu — auto-fill dari periode sebelumnya
            $table->decimal('luas_tanam_akhir_bulan_lalu', 10, 2)->nullable()->after('luas_lahan');
            // Luas Panen Belum Habis (kolom 5 SBS / TBF)
            $table->decimal('luas_panen_belum_habis', 10, 2)->nullable()->after('luas_tanam_akhir_bulan_lalu');
            // Luas Tanaman Akhir Bulan/Triwulan Ini — auto-kalkulasi
            $table->decimal('luas_tanam_akhir', 10, 2)->nullable()->after('luas_panen_belum_habis');
            // Produksi Belum Habis (kolom 10 SBS / TBF)
            $table->decimal('produksi_belum_habis', 10, 2)->nullable()->after('luas_tanam_akhir');
            // Harga Jual rata-rata per Kg (kolom 11/12 semua form SPH)
            $table->decimal('harga_jual', 12, 2)->nullable()->after('produksi_belum_habis');

            // ── SPH-BST: kolom berbasis pohon/rumpun ─────────────────────────────
            // Jumlah Tanaman Akhir Triwulan Lalu — auto-fill dari triwulan sebelumnya
            $table->integer('jumlah_tanaman_akhir_triwulan_lalu')->nullable()->after('harga_jual');
            // Tanaman Dibongkar/Ditebang (kolom 4 BST)
            $table->integer('tanaman_dibongkar')->nullable()->after('jumlah_tanaman_akhir_triwulan_lalu');
            // Penanaman Baru (kolom 5 BST)
            $table->integer('tanaman_baru')->nullable()->after('tanaman_dibongkar');
            // Tanaman Belum Menghasilkan (kolom 7 BST)
            $table->integer('tanaman_tidak_menghasilkan')->nullable()->after('tanaman_baru');
            // Tanaman Tua/Rusak (kolom 9 BST)
            $table->integer('tanaman_tus_rusak')->nullable()->after('tanaman_tidak_menghasilkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_produksis', function (Blueprint $table) {
            $table->dropColumn([
                'luas_tanam_akhir_bulan_lalu',
                'luas_panen_belum_habis',
                'luas_tanam_akhir',
                'produksi_belum_habis',
                'harga_jual',
                'jumlah_tanaman_akhir_triwulan_lalu',
                'tanaman_dibongkar',
                'tanaman_baru',
                'tanaman_tidak_menghasilkan',
                'tanaman_tus_rusak',
            ]);
        });
    }
};
