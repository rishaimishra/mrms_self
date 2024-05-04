<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnquiriesPhone2ToDistricts extends Migration
{
    /**$table->string('enquiries_phone2')->nullable()->after('enquiries_phone');
            $t
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('districts', function (Blueprint $table) {
//            $table->string('enquiries_phone2')->nullable()->after('enquiries_phone');
//            $table->string('collection_point2')->nullable()->after('collection_point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('enquiries_phone2');
            $table->dropColumn('collection_point2');
        });
    }
}
