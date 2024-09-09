<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryTable extends Migration
{
	public function up()
	{
		Schema::create('country', function (Blueprint $table) {
			$table->id();
			$table->string('country_name');
			$table->string('country_phone_code', 10)->nullable();
			$table->string('country_iso_code', 20);
			$table->string('nationality', 100)->nullable();
			$table->unsignedInteger('order')->nullable();
			$table->text('optional_name');
			$table->text('optional_nationality');
			$table->tinyInteger('option')->nullable();
			$table->unsignedTinyInteger('status')->default(1);
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('country');
	}
}
