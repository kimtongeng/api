<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTypeTable extends Migration
{
    public function up()
    {
        Schema::create('room_type', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_type');
    }
}
