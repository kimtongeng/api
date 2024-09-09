<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNewsVisitorsTableAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_visitors', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->nullable()->comment('0: Pending , 1: Join, 2: Leave')->after('contact_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_visitors', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
