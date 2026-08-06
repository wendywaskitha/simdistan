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
        Schema::create('laporan_pupuks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_pupuk_id')->constrained('toko_pupuks')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuans')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->timestamps();

            $table->unique(['toko_pupuk_id', 'bulan', 'tahun']);
        });

        Schema::create('kuota_tahunan_pupuks', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('jenis_pupuk_id')->constrained('jenis_pupuks')->onDelete('cascade');
            $table->decimal('jumlah', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['tahun', 'kecamatan_id', 'jenis_pupuk_id']);
        });

        Schema::create('laporan_pupuk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_pupuk_id')->constrained('laporan_pupuks')->onDelete('cascade');
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('jenis_pupuk_id')->constrained('jenis_pupuks')->onDelete('cascade');
            $table->decimal('penebusan', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['laporan_pupuk_id', 'kecamatan_id', 'jenis_pupuk_id'], 'laporan_pupuk_details_unique');
        });

        Schema::create('dokumen_alokasi_tahunans', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->unique();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pupuk_details');
        Schema::dropIfExists('laporan_pupuks');
        Schema::dropIfExists('kuota_tahunan_pupuks');
        Schema::dropIfExists('dokumen_alokasi_tahunans');
    }
};
