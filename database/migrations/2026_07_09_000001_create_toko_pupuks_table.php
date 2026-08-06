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
        Schema::create('toko_pupuks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('pemilik')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });

        Schema::create('toko_kecamatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_pupuk_id')->constrained('toko_pupuks')->onDelete('cascade');
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['toko_pupuk_id', 'kecamatan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toko_kecamatan');
        Schema::dropIfExists('toko_pupuks');
    }
};
