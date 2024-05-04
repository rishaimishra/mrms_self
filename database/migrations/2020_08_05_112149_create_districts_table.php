<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('council_name');
            $table->string('council_short_name');
            $table->string('primary_logo')->nullable();
            $table->string('secondary_logo')->nullable();
            $table->text('council_address')->nullable();
            $table->text('penalties_note')->nullable();
            $table->text('warning_note')->nullable();
            $table->string('collection_point')->nullable();
            $table->text('bank_details')->nullable();
            $table->string('chif_administrator_sign')->nullable();
            $table->string('ceo_sign')->nullable();
            $table->string('enquiries_email')->nullable();
            $table->string('enquiries_phone')->nullable();
            $table->text('feedback')->nullable();
            $table->string('sq_meter_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
    }
}
