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
        Schema::create('laporan_produksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_komoditas_id')->constrained('kategori_komoditas')->onDelete('cascade');
            $table->foreignId('kecamatan_id')->constrained()->onDelete('cascade');
            $table->foreignId('komoditas_id')->constrained()->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained()->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('luas_tanam', 12, 2)->default(0.00);
            $table->decimal('luas_panen', 12, 2)->default(0.00);
            $table->decimal('produktivitas', 12, 2)->default(0.00);
            $table->decimal('produksi', 12, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['kecamatan_id', 'komoditas_id', 'bulan', 'tahun'], 'laporan_produksi_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_produksis');
    }
};
