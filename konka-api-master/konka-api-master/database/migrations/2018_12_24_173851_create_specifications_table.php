<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('parent_code')->default(0)->comment('上级编码');
            $table->string('name')->comment('名称');
            $table->smallInteger('order')->default(0)->comment('排序');
            $table->smallInteger('level')->nullable()->comment('级别');
            $table->smallInteger('status')->comment('状态');
            $table->text('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('specifications');
    }
}
