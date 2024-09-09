<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_type_id');
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('commune_id')->nullable();
            $table->unsignedBigInteger('property_type_id')->nullable();
            $table->unsignedBigInteger('accommodation_type_id')->nullable();
            $table->unsignedBigInteger('sale_assistance_id')->nullable();
            $table->unsignedDecimal('sale_assistance_commission', 10, 2)->nullable();
            $table->unsignedDecimal('agency_commission', 10, 2)->nullable();
            $table->unsignedDecimal('ref_agency_commission', 10, 2)->nullable();
            $table->string('code');
            $table->string('name', 500);
            $table->string('image')->nullable();
            $table->text('youtube_link')->nullable();
            $table->text('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('description')->nullable();
            $table->text('payment_policy')->nullable();
            $table->text('project_development')->nullable();
            $table->text('phone')->nullable()->comment('Store: 123,123,123');
            $table->string('telegram_number', 100)->nullable();
            $table->string('telegram_qr_code')->nullable();
            $table->string('email')->nullable();
            $table->unsignedDecimal('price', 10, 2)->nullable();
            $table->integer('view_count')->default(0);
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('facilities')->nullable();
            $table->text('policy')->nullable();
            $table->unsignedTinyInteger('status')->default(0)->comment('0: Pending, 1: Approved, 2: Booking, 3: Completed Booking');
            $table->unsignedTinyInteger('active')->default(1)->comment('0: False, 1: True');
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
        Schema::dropIfExists('business');
    }
}
