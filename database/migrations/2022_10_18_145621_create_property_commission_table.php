<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_commission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('agency_id');
            $table->unsignedTinyInteger('agency_type')->comment('1: Base, 2: Referral');
            $table->unsignedBigInteger('property_asset_id')->nullable();
            $table->unsignedDecimal('commission_percentage', 10, 2);
            $table->unsignedDecimal('commission_amount', 10, 2);
            $table->unsignedDecimal('withdrawn_amount', 10, 2);
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
        Schema::dropIfExists('property_commission');
    }
}
