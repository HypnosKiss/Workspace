<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('refund_order_code')->comment('退货单编码');
            $table->string('product_code')->comment('产品编码');
            $table->string('name')->comment('产品名称');
            $table->string('sub_title')->nullable()->comment('子标题');
            $table->string('image')->comment('产品图片');
            $table->string('specifications')->comment('产品规格组');
            $table->decimal('price', 12, 2)->comment('产品价格');
            $table->integer('num')->comment('数量');
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
        Schema::dropIfExists('refund_order_products');
    }
}
