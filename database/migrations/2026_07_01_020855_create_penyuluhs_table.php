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
        Schema::create('penyuluhs', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->nullable()->unique();
            $table->string('telepon')->nullable();
            $table->foreignId('bpp_id')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyuluhs');
    }
};
