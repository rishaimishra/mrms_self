<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryColumnsInPropertyAssessmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_assessment_details', function (Blueprint $table) {
            $table->dateTime('demand_note_delivered_at')->after('assessment_images_1')->nullable();
            $table->string('demand_note_recipient_name')->after('demand_note_delivered_at')->nullable();
            $table->string('demand_note_recipient_mobile')->after('demand_note_recipient_name')->nullable();
            $table->string('demand_note_recipient_photo')->after('demand_note_recipient_mobile')->nullable();
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
            $table->dropColumn([
                'demand_note_delivered_at',
                'demand_note_recipient_name',
                'demand_note_recipient_mobile',
                'demand_note_recipient_photo'
            ]);
        });
    }
}
