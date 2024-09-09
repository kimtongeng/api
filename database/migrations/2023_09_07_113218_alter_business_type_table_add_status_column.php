<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBusinessTypeTableAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_type', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->comment('1: Enable, 0: Disable')->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_type', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
