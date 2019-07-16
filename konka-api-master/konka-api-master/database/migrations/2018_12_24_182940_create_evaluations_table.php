<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('user_code')->comment('用户编码');
            $table->string('product_code')->comment('产品编码');
            $table->string('specification_text')->comment('规格');
            $table->smallInteger('rate')->default(0)->comment('评分');
            $table->text('content')->nullable()->comment('评价内容');
            $table->text('images')->nullable()->comment('图片组');
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
        Schema::dropIfExists('evaluations');
    }
}
