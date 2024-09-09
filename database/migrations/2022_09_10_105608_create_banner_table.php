<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('type')->comment('1: Link, 2: Detail');
            $table->text('description')->nullable()->comment('Can be: Link, Detail, Account ID, Service ID, Service Category ID');
            $table->unsignedTinyInteger('platform_type')->comment('1: Web, 2: Mobile');
            $table->unsignedTinyInteger('image_type')->comment('1: Square, 2: Rectangle');
            $table->string('image');
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
        Schema::dropIfExists('banner');
    }
}
