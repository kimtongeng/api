<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBusinessBankAccountTableAddBusinessIdContactIdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_bank_account', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->after('bank_account_id');
            $table->unsignedBigInteger('contact_id')->after('business_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_bank_account', function (Blueprint $table) {
            $table->dropColumn('business_id');
            $table->dropColumn('contact_id');
        });
    }
}
