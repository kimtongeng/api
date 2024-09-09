<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterModifierTableAddChoiceIsRequiredColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modifier', function (Blueprint $table) {
            $table->unsignedTinyInteger('choice')->comment('1: Single, 2: Multi')->after('name');
            $table->unsignedTinyInteger('is_required')->comment('1: Yes, 0: No')->after('choice');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modifier', function (Blueprint $table) {
            $table->dropColumn('choice');
            $table->dropColumn('is_required');
        });
    }
}
