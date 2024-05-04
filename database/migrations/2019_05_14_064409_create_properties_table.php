<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('street_number')->nullable();
            $table->string('street_name')->nullable();
            $table->unsignedBigInteger('ward')->nullable();
            $table->string('constituency')->nullable();
            $table->string('section')->nullable();
            $table->string('chiefdom')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('postcode')->nullable();
            $table->boolean('is_completed')->nullable();
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
        Schema::dropIfExists('properties');
    }
}
