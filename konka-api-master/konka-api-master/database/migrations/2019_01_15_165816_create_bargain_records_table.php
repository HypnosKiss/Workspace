<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBargainRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bargain_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('open_id')->comment('用户的唯一标识');
            $table->string('user_bargain_product_code')->comment('被砍价的用户商品');
            $table->decimal('price',12,2)->comment('被砍价的价格');
            $table->string('nickname')->comment('砍价人的昵称');
            $table->string('avatar')->comment('砍价人的头像');
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
        Schema::dropIfExists('bargain_records');
    }
}
