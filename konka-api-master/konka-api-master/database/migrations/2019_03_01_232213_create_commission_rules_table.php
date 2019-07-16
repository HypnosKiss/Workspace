<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('规则编码');
            $table->string('name')->comment('规则名称');
            $table->smallInteger('type')->comment('规则类型');
            $table->dateTime('begin_time')->comment('开始时间');
            $table->dateTime('end_time')->comment('结束时间');
            $table->decimal('first_level_commission_percentage', 6, 2)->default(0)->comment('一级分佣比例');
            $table->decimal('second_level_commission_percentage', 6, 2)->default(0)->comment('二级分佣比例');
            $table->smallInteger('order')->default(0)->comment('优先级');
            $table->smallInteger('status')->default(10)->comment('状态');
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
        Schema::dropIfExists('commission_rules');
    }
}
