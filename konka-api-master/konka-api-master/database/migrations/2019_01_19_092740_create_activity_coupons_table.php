<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->smallInteger('type')->comment('类型');
            $table->dateTime('start_at')->comment('开始时间');
            $table->dateTime('end_at')->comment('结束时间');
            $table->smallInteger('status')->comment('状态');
            $table->string('create_person_code')->comment('创建人编码');
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
        Schema::dropIfExists('activity_coupons');
    }
}
