<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyPropertyTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_property_type', function (Blueprint $table) {
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('property_type_id');

           // $table->foreign('property_id')->references('id')->on('properties')->onDelete('CASCADE');
           // $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_property_types');
    }
}
