<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('code', 100)->change();
            $table->index('code');
            $table->index('is_hot');
            $table->index('is_new');
            $table->index('is_recommend');
        });

        Schema::table('product_specifications', function (Blueprint $table) {
            $table->string('product_code', 100)->change();
            $table->index('product_code');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->string('product_code', 100)->change();
            $table->index('product_code');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('code', 100)->change();
            $table->index('code');
            $table->index('level');
        });

        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('code', 100)->change();
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
