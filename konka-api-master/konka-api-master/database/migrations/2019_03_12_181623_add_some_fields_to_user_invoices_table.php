<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldsToUserInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_invoices', function (Blueprint $table) {
            $table->smallInteger('invoice_type')->default(10)->comment('发票类型');
            $table->smallInteger('material_type')->default(10)->comment('材质类型:电子/纸质');
            $table->string('name')->nullable()->comment('收货人名称');
            $table->string('phone')->nullable()->comment('收货人电话');
            $table->string('province')->nullable()->comment('省中文');
            $table->string('city')->nullable()->comment('市中文');
            $table->string('county')->nullable()->comment('县中文');
            $table->string('address')->nullable()->comment('详细地址');
            $table->string('send_email')->nullable()->comment('电子发票发送电子邮箱');
            $table->string('send_mobile')->nullable()->comment('电子发票通知手机号码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_invoices', function (Blueprint $table) {
            //
        });
    }
}
