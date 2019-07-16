<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerCommissionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_commission_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code')->comment('订单编号');
            $table->smallInteger('type')->comment('佣金类型:1级、2级');
            $table->decimal('order_amount', 10, 2)->comment('订单金额');
            $table->decimal('order_pay_amount', 10, 2)->comment('订单实际支付金额');
            $table->decimal('convert_ratio', 5, 2)->default(1)->comment('换算比例1元兑多少K币');
            $table->decimal('integral')->default(0)->comment('产生K币');
            $table->dateTime('should_unlock_time')->comment('预计解冻时间');
            $table->dateTime('unlocked_at')->nullable()->comment('实际解冻时间');
            $table->string('partner_code')->comment('合伙人编码');
            $table->smallInteger('status')->comment('佣金状态');
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
        Schema::dropIfExists('partner_commission_records');
    }
}
