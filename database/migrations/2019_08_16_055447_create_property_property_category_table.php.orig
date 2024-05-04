<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyPropertyCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_property_category', function (Blueprint $table) {
            $table->unsignedInteger('property_id');
            $table->unsignedInteger('property_category_id');

//            $table->foreign('property_id')
//                ->references('id')->on('properties')
//                ->onDelete('cascade');
//
//            $table->foreign('property_category_id')
//                ->references('id')->on('property_categories')
//                ->onDelete('cascade');
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
            //
        });
    }
}
