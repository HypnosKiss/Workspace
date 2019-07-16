<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundOrderFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_order_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('refund_order_code')->comment('退货单编码');
            $table->smallInteger('type')->comment('类型');
            $table->text('content')->comment('内容');
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
        Schema::dropIfExists('refund_order_feedbacks');
    }
}
