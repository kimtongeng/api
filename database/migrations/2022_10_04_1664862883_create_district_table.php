<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictTable extends Migration
{
    public function up()
    {
        Schema::create('district', function (Blueprint $table) {

            $table->id();
            $table->string('district_name');
            $table->unsignedBigInteger('province_id');
            $table->unsignedInteger('order')->nullable();
            $table->text('optional_name');
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('district');
    }
}
