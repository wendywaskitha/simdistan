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
        Schema::table('petanis', function (Blueprint $table) {
            $table->decimal('luas_lahan', 10, 2)->default(0)->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petanis', function (Blueprint $table) {
            $table->dropColumn('luas_lahan');
        });
    }
};
