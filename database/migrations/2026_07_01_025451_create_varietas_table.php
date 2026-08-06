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
        Schema::create('varietas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komoditas_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['komoditas_id', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('varietas');
    }
};
