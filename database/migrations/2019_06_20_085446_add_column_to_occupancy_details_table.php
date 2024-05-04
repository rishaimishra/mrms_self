<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToOccupancyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('occupancy_details', function (Blueprint $table) {
            $table->dropColumn(['owned_tenancy', 'rented','unoccupied_house']);
            //$table->enum('type',['Owned Tenancy', 'Rented House', 'Unoccupied House'])->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('occupancy_details', function (Blueprint $table) {

        });
    }
}
