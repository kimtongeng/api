<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRoomTableAddDiscountTypeDiscountAmountTotalPriceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('room', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->nullable()->after('price');
            $table->unsignedTinyInteger('discount_type')->nullable()->comment('1: Amount, 2: Percentage')->after('discount_amount');
            $table->decimal('total_price', 10, 2)->after('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('room', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_type');
            $table->dropColumn('total_price');
        });
    }
}
