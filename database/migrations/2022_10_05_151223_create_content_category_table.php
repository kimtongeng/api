<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_category', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Store JSON (Local Name and Latin Name)');
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('type')->default(1)->comment('1: Menu, 2: Page Content, ...');
            $table->unsignedInteger('parent_id')->default(0);
            $table->unsignedTinyInteger('status')->default(1)->comment('0: Disable, 1: Enable');
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
        Schema::dropIfExists('content_category');
    }
}
