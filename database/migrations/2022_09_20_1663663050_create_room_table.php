<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomTable extends Migration
{
    public function up()
    {
        Schema::create('room', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('room_type_id');
            $table->string('image')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room');
    }
}
