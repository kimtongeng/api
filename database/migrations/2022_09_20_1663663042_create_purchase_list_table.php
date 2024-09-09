<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseListTable extends Migration
{
	public function up()
	{
		Schema::create('purchase_list', function (Blueprint $table) {

			$table->id();
			$table->unsignedBigInteger('purchase_id');
			$table->unsignedBigInteger('product_id');
			$table->decimal('cost', 10, 2);
			$table->decimal('qty', 10, 2);
			$table->decimal('total_price', 12, 2);
			$table->text('remark')->nullable();
			$table->unsignedTinyInteger('status')->default(1);
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('purchase_list');
	}
}
