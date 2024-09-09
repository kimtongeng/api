<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionTableAddCustomerNameCustomerPhoneCustomerIdNoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->comment('Property Only')->after('customer_id');
            $table->string('customer_phone')->nullable()->comment('Property Only')->after('customer_name');
            $table->string('customer_id_card')->nullable()->comment('Property Only')->after('customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn('customer_name');
            $table->dropColumn('customer_phone');
            $table->dropColumn('customer_id_card');
        });
    }
}
