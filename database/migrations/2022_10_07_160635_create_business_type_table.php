<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_type_id');
            $table->string('name')->comment('1: Property, 2: Accommodation, 3: Delivery, 4: Shop Retail, 5: Shop Wholesale, 6: Restaurant, 7: Attraction, 8: News');
            $table->unsignedInteger('has_transaction')->comment('1: Yes, 0: NO');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_type');
    }
}
