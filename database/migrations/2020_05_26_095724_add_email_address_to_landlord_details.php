<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailAddressToLandlordDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('landlord_details', function (Blueprint $table) {
            $table->string('email')->nullable()->after('surname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landlord_details', function (Blueprint $table) {
            Schema::table('landlord_details', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        });
    }
}
