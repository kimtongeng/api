<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPropertyCommissionTableRenameCommssionPercentageColumnToCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_commission', function (Blueprint $table) {
            $table->renameColumn('commission_percentage', 'commission');
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
            $table->renameColumn('commission', 'commission_percentage');
        });
    }
}
