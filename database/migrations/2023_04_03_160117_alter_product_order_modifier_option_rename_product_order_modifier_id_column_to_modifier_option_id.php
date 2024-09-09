<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductOrderModifierOptionRenameProductOrderModifierIdColumnToModifierOptionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_order_modifier_option', function (Blueprint $table) {
            $table->renameColumn('product_order_modifier_id', 'modifier_option_id');
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
            $table->renameColumn('modifier_option_id', 'product_order_modifier_id');
        });
    }
}
