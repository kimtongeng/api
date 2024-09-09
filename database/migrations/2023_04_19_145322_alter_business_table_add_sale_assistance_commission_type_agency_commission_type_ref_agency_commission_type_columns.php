<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBusinessTableAddSaleAssistanceCommissionTypeAgencyCommissionTypeRefAgencyCommissionTypeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business', function (Blueprint $table) {
            $table->unsignedTinyInteger('sale_assistance_commission_type')->nullable()->comment('1: Amount, 2: Percentage')->after('sale_assistance_commission');
            $table->unsignedTinyInteger('agency_commission_type')->nullable()->comment('1: Amount, 2: Percentage')->after('agency_commission');
            $table->unsignedTinyInteger('ref_agency_commission_type')->nullable()->comment('1: Amount, 2: Percentage')->after('ref_agency_commission');
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
            $table->dropColumn('sale_assistance_commission_type');
            $table->dropColumn('agency_commission_type');
            $table->dropColumn('ref_agency_commission_type');
        });
    }
}
