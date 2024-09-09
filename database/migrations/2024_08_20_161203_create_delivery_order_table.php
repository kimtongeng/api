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
        Schema::create('delivery_order', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 50);
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('vehicle_type_id');
            $table->unsignedBigInteger('document_type_id');
            $table->string('image', 255)->nullable();
            $table->dateTime('transaction_date');
            $table->text('driver_location_link');
            $table->text('sender_location_link');
            $table->string('sender_name', 100);
            $table->string('sender_phone', 255);
            $table->text('sender_note')->nullable();
            $table->unsignedTinyInteger('payer')->comment('1: Sender, 2: Recipient');
            $table->unsignedTinyInteger('payment_method')->comment('1: Cash');
            $table->unsignedTinyInteger('payment_status')->comment('1: Pending, 2: Paid');
            $table->decimal('total_duration', 10, 2);
            $table->decimal('total_distance', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('cancel_reason');
            $table->unsignedTinyInteger('status')->comment('1: Pending, 2: Driver Accepted, 3: Driver picked up the parcel, 4: Enroute, 5: Devliered, 8: Driver Cancel Order, 9: Customer Cancel Order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order');
    }
};
