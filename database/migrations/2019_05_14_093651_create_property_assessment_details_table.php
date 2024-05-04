<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_assessment_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id');
            //$table->string('property_categories',20)->nullable();
            $table->string('property_wall_materials',20)->nullable();
            $table->string('roofs_materials',20)->nullable();
            $table->string('property_dimension',20)->nullable();
            $table->double('property_rate_without_gst',24,4)->nullable();
            $table->double('property_gst',24,4)->nullable();
            $table->double('property_rate_with_gst',24,4)->nullable();
            $table->string('property_use',20)->nullable();
            $table->string('zone',20)->nullable();
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
        Schema::dropIfExists('property_assessment_details');
    }
}
