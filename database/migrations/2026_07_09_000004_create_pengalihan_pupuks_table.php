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
        Schema::create('pengalihan_pupuks', function (Blueprint $table) {
            $table->id();
            $table->integer('bulan');
            $table->integer('tahun');
            $table->foreignId('jenis_pupuk_id')->constrained('jenis_pupuks')->onDelete('cascade');
            $table->foreignId('kecamatan_asal_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('kecamatan_tujuan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->decimal('jumlah', 12, 2)->default(0.00);
            $table->string('nama_sk')->nullable();
            $table->string('file_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengalihan_pupuks');
    }
};
