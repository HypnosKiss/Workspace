<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCapitalFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capital_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_code')->comment('用户编码');
            $table->string('currency')->nullable()->comment('币种');
            $table->decimal('money_change', 12, 2)->comment('操作金额');
            $table->decimal('final_money', 12, 2)->comment('操作后现金余额');
            $table->smallInteger('type')->comment('资金流类型');
            $table->string('order_code')->nullable()->comment('订单编码');
            $table->smallInteger('document_type')->nullable()->comment('来源单据类型');
            $table->string('document_number')->nullable()->comment('来源单据号');
            $table->text('remarks')->nullable()->comment('备注');
            $table->smallInteger('status')->comment('状态');
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
        Schema::dropIfExists('capital_flows');
    }
}
