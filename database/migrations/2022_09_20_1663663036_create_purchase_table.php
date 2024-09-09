<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTable extends Migration
{
    public function up()
    {
        Schema::create('purchase', function (Blueprint $table) {

            $table->id();
            $table->string('code', 10)->nullable();
            $table->string('ref_number', 20)->nullable();
            $table->datetime('purchase_date')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->text('remark')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase');
    }
}
