<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterModifierOptionRenameModifierGroupIdColumnToModifierId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modifier_option', function (Blueprint $table) {
            $table->renameColumn('modifier_group_id', 'modifier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modifier_option', function (Blueprint $table) {
            $table->renameColumn('modifier_id', 'modifier_group_id');
        });
    }
}
