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
        Schema::create('komoditas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_komoditas_id')->constrained('kategori_komoditas')->onDelete('cascade');
            $table->string('nama');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['kategori_komoditas_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komoditas');
    }
};
