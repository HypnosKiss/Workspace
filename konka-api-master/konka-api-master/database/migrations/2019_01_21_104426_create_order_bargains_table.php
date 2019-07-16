<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderBargainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bargains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code')->comment('订单编号');
            $table->string('user_bargain_product_code')->comment('用户砍价编号');
            $table->integer('bargain_people')->comment('砍价人数')->default(0);
            $table->decimal('price',12,2)->comment('砍价后的价格');
            $table->decimal('begin_price',12, 2)->comment('固定价格');
            $table->decimal('float_price',12, 2)->comment('浮动价格');
            $table->string('product_code')->comment('砍价商品编号');
            $table->string('product_specifications_code')->comment('产品规格编码');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
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
        Schema::dropIfExists('order_bargains');
    }
}
