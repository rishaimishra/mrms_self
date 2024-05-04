<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSwimmingToPropertyAssessmentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_assessment_details', function (Blueprint $table) {
            $table->unsignedBigInteger('swimming_id')->nullable()->after('gated_community');

          //  $table->foreign('swimming_id')->references('id')->on('swimmings')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_assessment_details', function (Blueprint $table) {
            //
        });
    }
}
