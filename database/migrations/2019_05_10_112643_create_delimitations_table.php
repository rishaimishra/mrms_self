<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelimitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delimitations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('ward');
            $table->unsignedInteger('constituency');
            $table->string('section');
            $table->string('district');
            $table->string('province');
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
        Schema::dropIfExists('delimitations');
    }
}
