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
        Schema::create('jenis_alats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('alsintans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained('kelompok_tanis')->onDelete('cascade');
            $table->string('nama_ketua')->nullable();
            $table->string('nama_operator')->nullable();
            $table->string('no_hp_operator')->nullable();
            $table->foreignId('jenis_alat_id')->constrained('jenis_alats')->onDelete('cascade');
            $table->string('nama_alat');
            $table->string('merek')->nullable();
            $table->string('kondisi')->default('Baik'); // Baik, Rusak Ringan, Rusak Berat
            $table->string('nomor_rangka')->nullable();
            $table->string('nomor_mesin')->nullable();
            $table->string('sumber_dana'); // APBD, APBN, DAK, BANPER, MANDIRI
            $table->decimal('harga', 15, 2)->default(0.00);
            $table->integer('tahun_bantuan');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('laporan_pemanfaatan_alsintans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alsintan_id')->constrained('alsintans')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('luas_lahan', 10, 2); // dalam Hektar (Ha)
            $table->integer('waktu_pengerjaan'); // dalam Jam
            $table->decimal('biaya_pengolahan', 15, 2)->default(0.00);
            $table->decimal('hour_meter', 10, 2); // nilai Hour Meter
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('realokasi_alsintans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alsintan_id')->constrained('alsintans')->onDelete('cascade');
            $table->foreignId('kelompok_tani_asal_id')->constrained('kelompok_tanis')->onDelete('cascade');
            $table->foreignId('kelompok_tani_tujuan_id')->constrained('kelompok_tanis')->onDelete('cascade');
            $table->date('tanggal_realokasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realokasi_alsintans');
        Schema::dropIfExists('laporan_pemanfaatan_alsintans');
        Schema::dropIfExists('alsintans');
        Schema::dropIfExists('jenis_alats');
    }
};
