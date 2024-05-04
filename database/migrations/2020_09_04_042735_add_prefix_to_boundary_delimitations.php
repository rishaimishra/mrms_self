<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrefixToBoundaryDelimitations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boundary_delimitations', function (Blueprint $table) {
           // $table->string('council')->nullable()->after('province');
            //$table->string('prefix')->nullable()->after('council');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boundary_delimitations', function (Blueprint $table) {
            $table->dropColumn('prefix');
            $table->dropColumn('council');
        });
    }
}
