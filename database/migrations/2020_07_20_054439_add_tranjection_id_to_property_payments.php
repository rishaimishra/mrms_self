<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTranjectionIdToPropertyPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_payments', function (Blueprint $table) {
            $table->string('transaction_id')->after('cheque_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_payments', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
            ]);
        });
    }
}
