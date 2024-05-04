<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPropertyAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_assessment_details', function (Blueprint $table) {
            $table->string('assessment_images_1')->nullable()->after('zone');
            $table->string('assessment_images_2')->nullable()->after('zone');
            $table->string('compound_name')->nullable()->after('zone');
            $table->string('no_of_compound_house')->nullable()->after('zone');
            $table->string('no_of_shop')->nullable()->after('zone');
            $table->string('no_of_mast')->nullable()->after('zone');
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
