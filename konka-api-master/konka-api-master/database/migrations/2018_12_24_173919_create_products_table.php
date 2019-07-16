<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('konka_product_code')->nullable()->comment('康佳内部系统产品编码');
            $table->string('product_category_code')->comment('产品分类编码');
            $table->string('title')->comment('标题');
            $table->string('sub_title')->nullable()->comment('子标题');
            $table->smallInteger('order')->default(0)->comment('排序');
            $table->smallInteger('status')->comment('状态');
            $table->integer('sales')->default(0)->comment('销量');
            $table->smallInteger('is_hot')->comment('是否热卖');
            $table->smallInteger('is_recommend')->comment('是否推荐');
            $table->smallInteger('is_new')->comment('是否新品');
            $table->smallInteger('min')->nullable()->comment('最小购买');
            $table->smallInteger('max')->nullable()->comment('最大购买');
            $table->smallInteger('per')->nullable()->comment('每次购买数');
            $table->dateTime('start_at')->nullable()->comment('开始时间');
            $table->dateTime('end_at')->nullable()->comment('结束时间');
            $table->text('specification_array')->comment('规格组');
            $table->string('create_person_code')->comment('创建人');
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
        Schema::dropIfExists('products');
    }
}
