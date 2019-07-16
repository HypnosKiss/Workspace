<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->smallInteger('position')->comment('位置');
            $table->string('title')->nullable()->comment('标题');
            $table->text('content')->nullable()->comment('内容');
            $table->string('extend')->nullable()->comment('扩展');
            $table->smallInteger('order')->default(0)->comment('排序');
            $table->smallInteger('status')->comment('状态');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->text('connect')->nullable()->comment('跳转地址');
            $table->smallInteger('connect_type')->nullable()->comment('跳转类型');
            $table->smallInteger('type')->nullable()->comment('广告类型');
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
        Schema::dropIfExists('advertisements');
    }
}
