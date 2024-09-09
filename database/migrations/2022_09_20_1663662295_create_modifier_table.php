<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModifierTable extends Migration
{
    public function up()
    {
        Schema::create('modifier', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('name');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modifier');
    }
}
