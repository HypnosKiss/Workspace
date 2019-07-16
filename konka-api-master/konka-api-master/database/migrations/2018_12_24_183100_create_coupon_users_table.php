<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('user_code')->comment('用户编码');
            $table->string('coupon_code')->comment('优惠券编码');
            $table->smallInteger('type')->comment('类型');
            $table->string('name')->comment('名称');
            $table->decimal('discount', 4, 1)->comment('折扣');
            $table->string('content')->nullable()->comment('内容');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->smallInteger('conditions')->comment('条件(满减、不限)');
            $table->decimal('conditions_value')->nullable()->comment('条件值');
            $table->smallInteger('status')->comment('状态');
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
        Schema::dropIfExists('coupon_users');
    }
}
