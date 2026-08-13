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
        Schema::create('target_tanams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->foreignId('komoditas_id')->constrained('komoditas')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->decimal('target', 12, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['kecamatan_id', 'komoditas_id', 'tahun', 'bulan'], 'target_tanam_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_tanams');
    }
};
