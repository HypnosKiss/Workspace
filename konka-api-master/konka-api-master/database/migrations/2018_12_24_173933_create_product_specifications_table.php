<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->text('specification_codes')->comment('规格编码组');
            $table->string('product_code')->comment('产品编码');
            $table->decimal('price', 12, 2)->comment('价格');
            $table->decimal('discount_price', 12, 2)->nullable()->comment('优惠价格');
            $table->decimal('distribution_price', 12, 2)->nullable()->comment('分销价格');
            $table->decimal('distribution_num', 6, 2)->nullable()->comment('分销点数');
            $table->integer('stock')->comment('库存');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_specifications');
    }
}
