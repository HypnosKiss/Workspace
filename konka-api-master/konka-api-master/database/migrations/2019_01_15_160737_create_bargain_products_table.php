<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBargainProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bargain_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('product_code')->comment('产品编码');
            $table->string('product_specifications_code')->comment('产品规格编码');
            $table->decimal('after_price', 12, 2)->comment('砍价后价格');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->integer('bargain_num')->default(0)->comment('砍价人数');
            $table->decimal('begin_price',12, 2)->comment('固定价格');
            $table->decimal('float_price',12, 2)->comment('浮动价格');
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
        Schema::dropIfExists('bargain_products');
    }
}
