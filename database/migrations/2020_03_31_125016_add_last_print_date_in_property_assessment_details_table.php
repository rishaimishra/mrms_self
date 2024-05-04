<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastPrintDateInPropertyAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_assessment_details', function (Blueprint $table) {
            $table->dateTime('last_printed_at')->nullable()->after('assessment_images_1');
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
            $table->dropColumn('last_printed_at');
        });
    }
}
