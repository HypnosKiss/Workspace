<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('user_code')->comment('用户编码');
            $table->smallInteger('type')->comment('类型');
            $table->string('order_code')->comment('订单编码');
            $table->smallInteger('refund_type')->comment('退货方式');
            $table->text('content')->comment('问题描述');
            $table->text('images')->nullable()->comment('问题图片');
            $table->decimal('price', 12, 2)->nullable()->comment('退货金额');
            $table->smallInteger('status')->comment('状态');
            $table->string('tracking_type')->nullable()->comment('快递类型');
            $table->string('tracking_number')->nullable()->comment('快递单号');
            $table->softDeletes();
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
        Schema::dropIfExists('refund_orders');
    }
}
