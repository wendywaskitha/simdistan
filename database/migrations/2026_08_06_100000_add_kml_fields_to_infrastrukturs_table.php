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
        Schema::table('infrastrukturs', function (Blueprint $table) {
            $table->string('kml_file')->nullable()->after('longitude');
            $table->longText('geojson')->nullable()->after('kml_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('infrastrukturs', function (Blueprint $table) {
            $table->dropColumn(['kml_file', 'geojson']);
        });
    }
};
