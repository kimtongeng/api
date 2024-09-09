<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductModifierTable extends Migration
{
    public function up()
    {
        Schema::create('product_modifier', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('modifier_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_modifier');
    }
}
