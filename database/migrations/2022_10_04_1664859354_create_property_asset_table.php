<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyAssetTable extends Migration
{
    public function up()
    {
        Schema::create('property_asset', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('asset_category_id');
            $table->string('image')->nullable();
            $table->string('code', 50);
            $table->decimal('size', 8, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment('1: Booking, 2: Completed Booking');
            $table->unsignedTinyInteger('active')->default(1)->comment('0: False, 1: True');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_asset');
    }
}
