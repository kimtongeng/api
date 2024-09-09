<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderModifierTable extends Migration
{
    public function up()
    {
        Schema::create('product_order_modifier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_order_list_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_order_modifier');
    }
}
