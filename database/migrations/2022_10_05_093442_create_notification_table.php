<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('contact_broadcast_type')->nullable()->comment('1: ForSelf, 2: For Advertise');
            $table->unsignedTinyInteger('contact_noti_type')->nullable()->comment('1: Link, 2: Detail, ....');
            $table->unsignedInteger('contact_id')->nullable();
            $table->unsignedTinyInteger('admin_noti_type')->nullable()->comment('...');
            $table->unsignedInteger('reference_id')->nullable();
            $table->unsignedTinyInteger('business_type')->nullable()->comment('1: Property, 2: Accommodation, 3: Delivery, 4: Shop Retail, 5: Shop Wholesale, 6: Restaurant, 7: Attraction, 8: News');
            $table->unsignedInteger('created_by');
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
        Schema::dropIfExists('notification');
    }
}
