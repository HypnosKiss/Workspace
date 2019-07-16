<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('parent_code')->nullable()->comment('上级编码');
            $table->string('name')->comment('名称');
            $table->string('image')->nullable()->comment('图片');
            $table->smallInteger('level')->nullable()->comment('级别');
            $table->smallInteger('order')->default(0)->comment('排序');
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
        Schema::dropIfExists('product_categories');
    }
}
