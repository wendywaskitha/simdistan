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
        Schema::create('infrastrukturs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek');
            $table->string('jenis_infrastruktur'); // Jaringan Irigasi Tersier, Embung, Jalan Usaha Tani, Sumur Bor, Dam Parit, dll.
            $table->foreignId('kelompok_tani_id')->nullable()->constrained('kelompok_tanis')->onDelete('set null');
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade');
            $table->decimal('volume', 12, 2)->default(0.00);
            $table->string('satuan')->default('Unit'); // Meter, Unit, m3, dll.
            $table->decimal('nilai_anggaran', 15, 2)->default(0.00);
            $table->string('sumber_dana'); // APBD, APBN, DAK, BANPER, MANDIRI, dll.
            $table->integer('tahun_anggaran');
            $table->string('status_pembangunan')->default('Rencana'); // Rencana, Konstruksi, Selesai, Rusak
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('infrastruktur_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infrastruktur_id')->constrained('infrastrukturs')->onDelete('cascade');
            $table->date('tanggal_laporan');
            $table->string('kondisi')->default('Baik'); // Baik, Rusak Ringan, Rusak Berat
            $table->decimal('progres_fisik', 5, 2)->default(0.00); // 0.00 % s.d 100.00 %
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastruktur_laporans');
        Schema::dropIfExists('infrastrukturs');
    }
};
