<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacePriceListTable extends Migration
{
    public function up()
    {
        Schema::create('place_price_list', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('image')->nullable();
            $table->string('name')->nullable();
            $table->decimal('price', 10, 2);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('place_price_list');
    }
}
