<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedTinyInteger('is_seller')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->unsignedTinyInteger('is_agency')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->unsignedTinyInteger('is_property_owner')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->unsignedTinyInteger('is_hotel_owner')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->unsignedTinyInteger('is_driver')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->unsignedTinyInteger('is_news')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->unsignedTinyInteger('is_sale_assistance')->comment('0: No, 1: Yes, 2: Suspend')->default(0);
            $table->string('fullname', 100);
            $table->string('phone')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('google', 100);
            $table->string('social_id')->nullable();
            $table->string('apple_id')->nullable();
            $table->unsignedTinyInteger('gender')->default(1)->comment('1: Male, 2: Female');
            $table->text('agency_phone')->nullable();
            $table->string('id_card', 100)->nullable();
            $table->string('passport_no', 100)->nullable();
            $table->string('id_card_image_front', 100)->nullable();
            $table->string('id_card_image_back', 100)->nullable();
            $table->string('profile_image', 100)->nullable();
            $table->string('cover_image', 100)->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('0: Disabled, 1: Enable');
            $table->unsignedBigInteger('referral_agency_id')->nullable();
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
        Schema::dropIfExists('contact');
    }
}
