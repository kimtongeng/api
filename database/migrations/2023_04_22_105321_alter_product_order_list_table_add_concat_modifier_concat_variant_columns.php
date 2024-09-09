<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductOrderListTableAddConcatModifierConcatVariantColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_order_list', function (Blueprint $table) {
            $table->text('concat_modifier')->nullable()->after('total_price');
            $table->text('concat_variant')->nullable()->after('concat_modifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_order_list', function (Blueprint $table) {
            $table->dropColumn('concat_modifier');
            $table->dropColumn('concat_variant');
        });
    }
}
