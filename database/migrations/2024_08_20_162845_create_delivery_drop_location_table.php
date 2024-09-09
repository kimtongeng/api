<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_drop_location', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_order_id');
            $table->string('drop_order_no', 50);
            $table->text('recipient_location_link');
            $table->string('recipient_name', 100);
            $table->string('recipient_phone', 255);
            $table->text('recipient_note')->nullable();
            $table->decimal('duration', 10, 2);
            $table->decimal('distance', 10, 2);
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_drop_location');
    }
};
