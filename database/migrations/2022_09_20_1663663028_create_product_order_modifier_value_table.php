<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOrderModifierValueTable extends Migration
{
    public function up()
    {
        Schema::create('product_order_modifier_value', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('product_order_list_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_order_modifier_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_order_modifier_value');
    }
}
