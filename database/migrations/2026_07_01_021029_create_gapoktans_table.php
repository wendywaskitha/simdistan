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
        Schema::create('gapoktans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('kecamatan_id')->constrained()->onDelete('cascade');
            $table->string('ketua')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Nama gapoktan di kecamatan yang sama harus unik
            $table->unique(['kecamatan_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gapoktans');
    }
};
