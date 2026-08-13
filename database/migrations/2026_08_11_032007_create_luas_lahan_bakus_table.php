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
        Schema::create('luas_lahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('komoditas_id')->constrained('komoditas')->onDelete('cascade');
            $table->integer('tahun');
            $table->decimal('luas_lahan', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['kecamatan_id', 'tahun', 'komoditas_id'], 'lahan_baku_komoditas_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luas_lahan_bakus');
    }
};
