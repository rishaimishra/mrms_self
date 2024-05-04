<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenLocationCodeColumnInPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_geo_registry', function (Blueprint $table) {
            $table->char('open_location_code')->index()->after('digital_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_geo_registry', function (Blueprint $table) {
            $table->dropColumn('open_location_code');
        });
    }
}
