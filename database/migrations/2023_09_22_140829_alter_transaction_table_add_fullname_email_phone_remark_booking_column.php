<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionTableAddFullnameEmailPhoneRemarkBookingColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('fullname')->nullable()->after('customer_id_card');
            $table->string('email')->nullable()->after('fullname');
            $table->string('phone')->nullable()->after('email');
            $table->text('remark_booking')->nullable()->after('remark');
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
            $table->dropColumn('fullname');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('remark_booking');
        });
    }
}
