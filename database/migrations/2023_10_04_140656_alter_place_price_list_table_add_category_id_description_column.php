<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPlacePriceListTableAddCategoryIdDescriptionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('place_price_list', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->after('business_id');
            $table->text('description')->nullable()->after('price');
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
            $table->dropColumn('category_id');
            $table->dropColumn('description');
        });
    }
}
