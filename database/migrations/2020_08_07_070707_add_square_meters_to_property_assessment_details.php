<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSquareMetersToPropertyAssessmentDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_assessment_details', function (Blueprint $table) {
            $table->string('length')->nullable()->after('property_dimension');
            $table->string('breadth')->nullable()->after('length');
            $table->string('square_meter')->nullable()->after('breadth');
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
            $table->dropColumn('length');
            $table->dropColumn('breadth');
            $table->dropColumn('square_meter');
        });
    }
}
