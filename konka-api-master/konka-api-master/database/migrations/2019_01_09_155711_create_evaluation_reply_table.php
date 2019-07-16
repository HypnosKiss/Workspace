<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluationReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('type')->comment('类型');
            $table->string('evaluation_code')->comment('评价编码');
            $table->text('content')->comment('评价内容');
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
        Schema::dropIfExists('evaluation_reply');
    }
}
