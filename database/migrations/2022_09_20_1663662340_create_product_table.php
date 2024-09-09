<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
	public function up()
	{
		Schema::create('product', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('country_id')->nullable();
			$table->unsignedBigInteger('business_id');
			$table->unsignedBigInteger('unit_type_id');
			$table->unsignedBigInteger('category_id')->nullable();
			$table->unsignedBigInteger('brand_id')->nullable();
			$table->unsignedBigInteger('model_id')->nullable();
			$table->string('code', 10)->nullable();
			$table->string('name');
			$table->string('sku', 20)->nullable();
			$table->string('tag')->nullable();
			$table->string('year', 4)->nullable();
			$table->unsignedTinyInteger('condition')->nullable();
			$table->decimal('price', 10, 2);
			$table->decimal('discount_amount', 10, 2)->nullable();
			$table->unsignedTinyInteger('discount_type')->nullable()->comment('1: Amount, 2: Percentage');
			$table->decimal('sell_price', 10, 2);
			$table->decimal('qty', 10, 2)->nullable()->default(0);
			$table->decimal('alert_qty', 10, 2)->nullable()->default(0);
			$table->string('image')->nullable();
			$table->string('variant_detail')->nullable();
			$table->unsignedBigInteger('parent_id')->nullable()->default(0);
			$table->unsignedTinyInteger('has_variant')->nullable()->default(0)->comment('1: Yes, 0: No');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::dropIfExists('product');
	}
}
