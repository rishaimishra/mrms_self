<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnfinishedPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unfinished_properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reason');
            $table->string('unfinished_property_image');
            $table->string('unfinished_property_lat');
            $table->string('unfinished_property_long');
            $table->string('enumerator');
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
        Schema::dropIfExists('unfinished_properties');
    }
}
