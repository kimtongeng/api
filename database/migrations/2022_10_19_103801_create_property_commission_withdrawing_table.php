<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyCommissionWithdrawingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_commission_withdrawing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_commission_id');
            $table->dateTime('withdraw_date');
            $table->unsignedDecimal('withdraw_amount', 10, 2);
            $table->datetime('transaction_date');
            $table->string('transaction_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdrawing_commission');
    }
}
