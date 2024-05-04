<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('is_organization')->default(0)->after('postcode');
            $table->string('organization_name')->nullable()->after('postcode');
            $table->string('organization_type')->nullable()->after('postcode');
            $table->string('organization_tin')->nullable()->after('postcode');
            $table->string('organization_addresss')->nullable()->after('postcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            //
        });
    }
}
