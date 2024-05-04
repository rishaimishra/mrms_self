<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePorpertyPropertyValueAddedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_property_value_added', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('property_value_added_id');

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('CASCADE');
            $table->foreign('property_value_added_id')->references('id')->on('property_value_added')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_property_value_added');
    }
}
