<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email',160)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('ward',5)->nullable();
            $table->string('constituency',5)->nullable();
            $table->string('section',30)->nullable();
            $table->string('chiefdom',30)->nullable();
            $table->string('district',30)->nullable();
            $table->string('province',30)->nullable();
            $table->string('gender',5)->nullable();
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
        Schema::dropIfExists('users');
    }
}
