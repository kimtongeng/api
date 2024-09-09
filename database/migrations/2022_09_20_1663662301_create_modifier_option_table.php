<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModifierOptionTable extends Migration
{
    public function up()
    {
        Schema::create('modifier_option', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('modifier_group_id');
            $table->string('name')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modifier_option');
    }
}
