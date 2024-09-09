<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductTableAddDescriptionIsDiscountIsTrackStockCloumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_discount')->nullable()->default(0)->comment('1: Yes, 0: No')->after('price');
            $table->unsignedTinyInteger('is_track_stock')->nullable()->default(0)->comment('1: Yes, 0: No')->after('sell_price');
            $table->text('description')->nullable()->after('has_variant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('is_discount');
            $table->dropColumn('is_track_stock');
            $table->dropColumn('description');
        });
    }
}
