<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommuneTable extends Migration
{
	public function up()
	{
		Schema::create('commune', function (Blueprint $table) {
			$table->id();
			$table->string('commune_name');
			$table->string('postal_code');
			$table->unsignedBigInteger('district_id');
			$table->unsignedInteger('order')->nullable();
			$table->text('optional_name');
			$table->unsignedBigInteger('status')->nullable()->default(1);
			$table->unsignedBigInteger('created_by')->nullable();
			$table->unsignedBigInteger('updated_by')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('commune');
	}
}
