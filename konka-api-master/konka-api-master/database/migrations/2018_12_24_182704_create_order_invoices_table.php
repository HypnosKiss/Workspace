<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code')->comment('订单编码');
            $table->smallInteger('invoice_type')->comment('发票类型');
            $table->smallInteger('type')->nullable()->comment('类型');
            $table->string('unit_name')->comment('名称');
            $table->string('tax_ticket')->nullable()->comment('税票号');
            $table->string('tax_ticket_address')->nullable()->comment('税票地址');
            $table->string('tax_ticket_phone')->nullable()->comment('税票电话');
            $table->string('open_bank')->nullable()->comment('开户行');
            $table->string('bank_account')->nullable()->comment('银行帐号');
            $table->string('name')->nullable()->comment('收货人名称');
            $table->string('phone')->nullable()->comment('收货人电话');
            $table->string('province_code')->nullable()->comment('省编码');
            $table->string('province_text')->nullable()->comment('省中文');
            $table->string('city_code')->nullable()->comment('市编码');
            $table->string('city_text')->nullable()->comment('市中文');
            $table->string('county_code')->nullable()->comment('县编码');
            $table->string('county_text')->nullable()->comment('县中文');
            $table->string('address')->nullable()->comment('详细地址');
            $table->string('image')->nullable()->comment('电子发票图片');
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
        Schema::dropIfExists('order_invoices');
    }
}
