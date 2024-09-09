<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('app_type_id');
            $table->unsignedBigInteger('business_type_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('business_owner_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->unsignedBigInteger('property_asset_id')->nullable();
            $table->datetime('transaction_date');
            $table->string('code', 50)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('sell_amount', 10, 2);
            $table->string('image')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->unsignedTinyInteger('transaction_fee')->nullable();
            $table->decimal('transaction_fee_amount', 10, 2)->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('1: Pending, 2: Approved, 3: Completed, 4: Rejected, 5: Cancelled ');
            $table->unsignedTinyInteger('transaction_fee_status')->default(0)->comment('0: Business owner not yet pay, 1: Paid');
            $table->date('check_in_date')->nullable()->comment('For accommodation business');
            $table->date('check_out_date')->nullable()->comment('For accommodation business');
            $table->unsignedTinyInteger('order_type')->nullable()->comment('Shop only[1: Takeout, 2: Delivery, 3: Dine-In]');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
