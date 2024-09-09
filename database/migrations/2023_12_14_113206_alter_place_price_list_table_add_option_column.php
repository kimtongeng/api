<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPlacePriceListTableAddOptionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('place_price_list', function (Blueprint $table) {
            $table->unsignedTinyInteger('option')->default(1)->comment('1: For Sale , 2: Not For Sale')->after('sell_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('place_price_list', function (Blueprint $table) {
            $table->dropColumn('option');
        });
    }
}
