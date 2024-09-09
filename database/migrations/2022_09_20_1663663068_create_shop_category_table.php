<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('shop_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_type_id');
            $table->string('name');
            $table->unsignedTinyInteger('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_category');
    }
}
