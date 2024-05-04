<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyPropertyTypesTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_property_types_total', function (Blueprint $table) {
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('property_type_id');
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->foreign('assessment_id')->references('id')->on('property_assessment_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_property_types_total');
    }
}
