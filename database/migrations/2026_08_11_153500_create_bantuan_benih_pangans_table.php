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
        Schema::create('bantuan_benih_pangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained()->onDelete('cascade');
            $table->foreignId('komoditas_id')->constrained()->onDelete('cascade');
            $table->foreignId('varietas_id')->nullable()->constrained()->onDelete('set null');
            $table->double('jumlah_bantuan');
            $table->string('satuan')->default('Kg');
            $table->string('sumber_dana');
            $table->integer('tahun_bantuan');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_benih_pangans');
    }
};
