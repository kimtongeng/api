<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_address', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('note')->nullable();
            $table->text('label')->nullable();
            $table->unsignedTinyInteger('is_default')->default(0)->comment('1: True, 0: False');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shpping_address_account');
    }
}
