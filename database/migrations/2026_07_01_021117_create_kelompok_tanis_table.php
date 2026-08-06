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
        Schema::create('kelompok_tanis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('desa_id')->constrained()->onDelete('cascade');
            $table->foreignId('gapoktan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ketua')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Nama kelompok tani di desa yang sama harus unik
            $table->unique(['desa_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_tanis');
    }
};
