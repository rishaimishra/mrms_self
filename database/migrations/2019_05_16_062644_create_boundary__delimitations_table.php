<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoundaryDelimitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boundary__delimitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ward');
            $table->bigInteger('constituency');
            $table->string('section');
            $table->string('chiefdom');
            $table->string('district');
            $table->string('province');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boundary__delimitations');
    }
}
