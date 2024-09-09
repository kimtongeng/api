<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyTypeTable extends Migration
{
    public function up()
    {
        Schema::create('property_type', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('type')->comment('1: Single, 2: Multi');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_type');
    }
}
