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
        Schema::create('bantuan_bibit_hortis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained()->onDelete('cascade');
            $table->foreignId('komoditas_id')->constrained()->onDelete('cascade');
            $table->double('jumlah_bantuan');
            $table->string('satuan')->default('Pohon');
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
        Schema::dropIfExists('bantuan_bibit_hortis');
    }
};
