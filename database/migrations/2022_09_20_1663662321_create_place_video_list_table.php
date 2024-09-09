<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaceVideoListTable extends Migration
{
    public function up()
    {
        Schema::create('place_video_list', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->text('link')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('place_video_list');
    }
}
