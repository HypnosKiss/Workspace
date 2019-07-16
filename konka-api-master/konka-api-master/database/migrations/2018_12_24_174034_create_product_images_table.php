<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_code')->nullable()->comment('产品编码');
            $table->string('product_specification_code')->nullable()->comment('产品规格编码');
            $table->string('image')->comment('图片');
            $table->smallInteger('order')->default(0)->comment('排序');
            $table->smallInteger('type')->comment('类型');
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
        Schema::dropIfExists('product_images');
    }
}
