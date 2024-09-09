<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookListTable extends Migration
{
    public function up()
    {
        Schema::create('book_list', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('transaction_id')->unsigned();
            $table->unsignedBigInteger('room_id')->unsigned();
            $table->decimal('price', 10, 2);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_list');
    }
}
