<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBargainProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bargain_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_code')->comment('用户编码');
            $table->string('bargain_product_code')->comment('砍价的商品编码');
            $table->decimal('bargain_price',12,2)->comment('已砍掉的价格');
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
        Schema::dropIfExists('user_bargain_products');
    }
}
