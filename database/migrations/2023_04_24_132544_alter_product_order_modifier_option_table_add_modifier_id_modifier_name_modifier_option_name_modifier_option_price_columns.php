<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductOrderModifierOptionTableAddModifierIdModifierNameModifierOptionNameModifierOptionPriceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_order_modifier_option', function (Blueprint $table) {
            $table->unsignedBigInteger('modifier_id')->after('product_id');
            $table->string('modifier_name')->after('modifier_id');
            $table->string('modifier_option_name')->after('modifier_option_id');
            $table->decimal('modifier_option_price')->after('modifier_option_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_order_modifier_option', function (Blueprint $table) {
            $table->dropColumn('modifier_id');
            $table->dropColumn('modifier_name');
            $table->dropColumn('modifier_option_name');
            $table->dropColumn('modifier_option_price');
        });
    }
}
