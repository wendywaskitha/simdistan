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
        Schema::create('bantuan_benih_pangan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bantuan_benih_pangan_id');
            $table->unsignedBigInteger('petani_id');
            $table->double('jumlah_bantuan');
            $table->timestamps();

            $table->foreign('bantuan_benih_pangan_id', 'fk_bbpd_parent')
                ->references('id')->on('bantuan_benih_pangans')
                ->onDelete('cascade');
            $table->foreign('petani_id', 'fk_bbpd_petani')
                ->references('id')->on('petanis')
                ->onDelete('cascade');
        });

        Schema::create('bantuan_bibit_horti_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bantuan_bibit_horti_id');
            $table->unsignedBigInteger('petani_id');
            $table->double('jumlah_bantuan');
            $table->timestamps();

            $table->foreign('bantuan_bibit_horti_id', 'fk_bbhd_parent')
                ->references('id')->on('bantuan_bibit_hortis')
                ->onDelete('cascade');
            $table->foreign('petani_id', 'fk_bbhd_petani')
                ->references('id')->on('petanis')
                ->onDelete('cascade');
        });

        Schema::create('bantuan_bibit_perkebunan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bantuan_bibit_perkebunan_id');
            $table->unsignedBigInteger('petani_id');
            $table->double('jumlah_bantuan');
            $table->timestamps();

            $table->foreign('bantuan_bibit_perkebunan_id', 'fk_bbpkd_parent')
                ->references('id')->on('bantuan_bibit_perkebunans')
                ->onDelete('cascade');
            $table->foreign('petani_id', 'fk_bbpkd_petani')
                ->references('id')->on('petanis')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_benih_pangan_details');
        Schema::dropIfExists('bantuan_bibit_horti_details');
        Schema::dropIfExists('bantuan_bibit_perkebunan_details');
    }
};
