<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixedRefundOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('refund_order_products');
        Schema::dropIfExists('refund_order_feedbacks');
        Schema::table('refund_orders', function (Blueprint $table) {
            $table->string('product_code')->comment('产品编码');
            $table->integer('num')->comment('退货件数');
            $table->string('refund_transaction_order')->nullable()->comment('退款交易单号');
            $table->string('product_title')->comment('商品名称');
            $table->string('product_sub_title')->comment('商品副标题');
            $table->string('product_image')->comment('商品图片');
            $table->decimal('product_price', 10, 2)->comment('商品售价');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refund_orders', function (Blueprint $table) {
            //
        });
    }
}
