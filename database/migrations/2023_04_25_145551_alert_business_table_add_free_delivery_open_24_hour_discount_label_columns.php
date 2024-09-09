<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlertBusinessTableAddFreeDeliveryOpen24HourDiscountLabelColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->unsignedTinyInteger('free_delivery')->nullable()->comment('Shop Only[1: Yes, 0: No]')->after('policy');
            $table->unsignedTinyInteger('open_24_hour')->nullable()->comment('Shop Only[1: Yes, 0: No]')->after('free_delivery');
            $table->string('discount_label')->nullable()->comment('Shop Only')->after('open_24_hour');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->dropColumn('free_delivery');
            $table->dropColumn('open_24_hour');
            $table->dropColumn('discount_label');
        });
    }
}
