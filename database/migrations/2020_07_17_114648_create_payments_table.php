<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('payee_name')->nullable();
            $table->text('payment_mode')->nullable();
            $table->double('amount')->nullable();
            $table->double('amount_in_le')->nullable();
            $table->boolean('is_complete')->default(false);


            $table->foreign('property_id')->references('id')->on('properties')->onDelete('CASCADE');
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
        Schema::dropIfExists('payments');
    }
}
