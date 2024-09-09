<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPropertyCommissionTableAddCommissionTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_commission', function (Blueprint $table) {
            $table->unsignedTinyInteger('commission_type')->comment('1: Amount, 2: Percentage')->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_commission', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });
    }
}
