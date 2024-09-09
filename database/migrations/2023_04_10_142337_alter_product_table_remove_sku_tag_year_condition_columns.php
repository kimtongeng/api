<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductTableRemoveSkuTagYearConditionColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('tag');
            $table->dropColumn('year');
            $table->dropColumn('condition');
            $table->dropColumn('variant_detail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('sku', 20)->nullable()->after('name');
            $table->string('tag')->nullable()->after('sku');
            $table->string('year', 4)->nullable()->after('tag');
            $table->unsignedTinyInteger('condition')->nullable()->after('year');
            $table->string('variant_detail')->nullable()->after('image_thumbnail');
        });
    }
}
