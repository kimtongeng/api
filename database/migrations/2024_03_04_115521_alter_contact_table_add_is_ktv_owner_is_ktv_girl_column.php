<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContactTableAddIsKtvOwnerIsKtvGirlColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->unsignedTinyInteger('is_ktv_owner')->comment('0:No, 1:Yes, 2:Suspend')->after('is_massager');
            $table->unsignedTinyInteger('is_ktv_girl')->comment('0:No, 1:Yes, 2:Suspend')->after('is_ktv_owner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('is_ktv_owner');
            $table->dropColumn('is_ktv_girl');
        });
    }
}
