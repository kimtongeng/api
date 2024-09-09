<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('post_by');
            $table->text('title')->comment('Store JSON (Local Name and Latin Name)');
            $table->string('image_thumbnail')->nullable();
            $table->text('short_desc')->nullable();
            $table->text('full_desc')->nullable();
            $table->integer('order')->default(1);
            $table->integer('view_count')->default(0);
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
        Schema::dropIfExists('post');
    }
}
