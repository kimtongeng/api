<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('position', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('page')->comment('1: Home');
            $table->unsignedTinyInteger('platform_type')->comment('1: Web, 2: Mobile');
            $table->integer('type')->comment('1: Banner, 2: Video');
            $table->unsignedInteger('reference_id')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('0: Disable, 1: Enable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('position');
    }
}
