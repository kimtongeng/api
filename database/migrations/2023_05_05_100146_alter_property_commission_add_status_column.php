<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPropertyCommissionAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_commission_withdrawing', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->after('property_commission_id');
            $table->unsignedTinyInteger('status')->default(0)->comment('0: Pending, 1: Confirm, 2: Reject')->after('transaction_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_commission_withdrawing', function (Blueprint $table) {
            $table->dropColumn('contact_id');
            $table->dropColumn('status');
        });
    }
}
