<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('create_user_code')->comment('用户编码');
            $table->smallInteger('type')->comment('类型');
            $table->smallInteger('pay_type')->comment('支付方式');
            $table->smallInteger('distribution')->comment('配送方式');
            $table->string('tracking_type')->nullable()->comment('快递类型');
            $table->string('tracking_number')->nullable()->comment('快递单号');
            $table->string('client_name')->comment('收货人名称');
            $table->string('client_phone')->comment('收货人电话');
            $table->string('province_code')->nullable()->comment('省编码');
            $table->string('province_text')->comment('省中文');
            $table->string('city_code')->nullable()->comment('市编码');
            $table->string('city_text')->comment('市中文');
            $table->string('county_code')->nullable()->comment('县编码');
            $table->string('county_text')->comment('县中文');
            $table->string('address')->comment('详细地址');
            $table->string('postal_code')->nullable()->comment('邮政编码');
            $table->string('user_coupon_code')->nullable()->comment('用户优惠券编码');
            $table->smallInteger('freight')->default(0)->comment('运费');
            $table->smallInteger('status')->comment('状态');
            $table->decimal('product_total_price', 14, 2)->comment('商品总金额');
            $table->decimal('discounted_price', 14, 2)->nullable()->comment('优惠金额');
            $table->dateTime('receive_at')->nullable()->comment('收货时间');
            $table->string('pay_number')->nullable()->comment('支付订单号');
            $table->string('pay_price')->nullable()->comment('支付金额');
            $table->dateTime('pay_at')->nullable()->comment('支付时间');
            $table->text('remarks')->nullable()->comment('备注');
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
        Schema::dropIfExists('orders');
    }
}
