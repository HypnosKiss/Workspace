<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('id_name')->nullable()->comment('真实姓名');
            $table->string('id_number')->nullable()->comment('身份证号码');
            $table->string('id_status')->nullable()->comment('实名认证状态');
            //
            $table->string('inline_name')->nullable()->comment('内部人员姓名');
            $table->string('inline_number')->nullable()->comment('内部人员编码');
            $table->string('first_department')->nullable()->comment('一级部门');
            $table->string('second_department')->nullable()->comment('二级部门');
            $table->string('third_department')->nullable()->comment('三级部门');
            //
            $table->string('handing_name')->nullable()->comment('经办名称');
            $table->string('network_name')->nullable()->comment('网点名称');
            $table->string('network_code')->nullable()->comment('网点编号');
            $table->string('parent_client_name')->nullable()->comment('上级客户名称');
            $table->string('parent_client_code')->nullable()->comment('上级客户编码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            //
        });
    }
}
