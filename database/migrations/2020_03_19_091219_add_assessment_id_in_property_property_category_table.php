<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssessmentIdInPropertyPropertyCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_property_category', function (Blueprint $table) {
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->foreign('assessment_id')->references('id')->on('property_assessment_details');
        });

        Schema::table('property_property_value_added', function (Blueprint $table) {
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->foreign('assessment_id')->references('id')->on('property_assessment_details');
        });

        Schema::table('property_property_type', function (Blueprint $table) {
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
        Schema::table('property_property_category', function (Blueprint $table) {
            $table->dropForeign('property_property_category_assessment_id_foreign');
            $table->dropColumn('assessment_id');
        });

        Schema::table('property_property_value_added', function (Blueprint $table) {
            $table->dropForeign('property_property_value_added_assessment_id_foreign');
            $table->dropColumn('assessment_id');
        });

        Schema::table('property_property_type', function (Blueprint $table) {
            $table->dropForeign('property_property_type_assessment_id_foreign');
            $table->dropColumn('assessment_id');
        });
    }
}
