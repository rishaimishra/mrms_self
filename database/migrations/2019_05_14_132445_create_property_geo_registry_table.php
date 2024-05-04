<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyGeoRegistryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_geo_registry', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id');
            $table->string('meter_number')->nullable();
            $table->string('meter_images')->nullable();
            $table->string('point1')->nullable();
            $table->string('point2')->nullable();
            $table->string('point3')->nullable();
            $table->string('point4')->nullable();
            $table->string('point5')->nullable();
            $table->string('point6')->nullable();
            $table->string('point7')->nullable();
            $table->string('point8')->nullable();
            $table->string('digital_address',160)->unique()->nullable();

            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_geo_registry');
    }
}
