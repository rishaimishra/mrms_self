<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToLandlordDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_details', function (Blueprint $table) {
            $table->string('tin')->nullable()->after('street_name');
            $table->string('id_type')->nullable()->after('street_name');
            $table->string('id_number')->nullable()->after('street_name');
            $table->string('image')->nullable()->after('street_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlord_details', function (Blueprint $table) {
            //
        });
    }
}
