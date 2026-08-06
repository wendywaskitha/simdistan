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
        Schema::create('laporan_produksi_mingguans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_produksi_id')->constrained('laporan_produksis')->onDelete('cascade');
            $table->integer('minggu_ke');
            $table->decimal('luas_tanam', 12, 2)->default(0.00);
            $table->decimal('luas_panen', 12, 2)->default(0.00);
            $table->decimal('produktivitas', 12, 2)->default(0.00);
            $table->decimal('produksi', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['laporan_produksi_id', 'minggu_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_produksi_mingguans');
    }
};
