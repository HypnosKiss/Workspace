<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->smallInteger('type')->comment('类型');
            $table->string('name')->comment('名称');
            $table->decimal('discount', 4, 1)->comment('折扣');
            $table->integer('num')->comment('数量');
            $table->string('content')->nullable()->comment('内容');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->smallInteger('conditions')->nullable()->comment('条件(满减)');
            $table->decimal('conditions_value')->nullable()->comment('条件值');
            $table->smallInteger('limit')->nullable()->comment('限领');
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
        Schema::dropIfExists('coupons');
    }
}
