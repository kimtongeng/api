<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPlacePriceListTableAddDiscountAmountDiscountTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('place_price_list', function (Blueprint $table) {
            $table->decimal('discount_amount',10)->nullable()->after('price');
            $table->unsignedTinyInteger('discount_type')->nullable()->after('discount_amount');
            $table->decimal('sell_price',10)->nullable()->after('discount_type');
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
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_type');
            $table->dropColumn('sell_price');
        });
    }
}
