<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteShopCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shop_category');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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
}
