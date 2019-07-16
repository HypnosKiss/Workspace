<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('user_code')->comment('用户编码');
            $table->string('parent_code')->nullable()->comment('上级合伙人编码');
            $table->string('parent_name')->nullable()->comment('上级合伙人名称');
            $table->string('parent_phone')->nullable()->comment('上级合伙人电话');
            $table->smallInteger('level')->nullable()->comment('级别');
            $table->decimal('points', 6, 2)->default(0)->comment('价格点数');
            $table->string('name')->comment('名称');
            $table->string('phone')->comment('电话');
            $table->string('province_code')->nullable()->comment('省编码');
            $table->string('province_text')->nullable()->comment('省中文');
            $table->string('city_code')->nullable()->comment('市编码');
            $table->string('city_text')->nullable()->comment('市中文');
            $table->string('county_code')->nullable()->comment('县编码');
            $table->string('county_text')->nullable()->comment('县中文');
            $table->string('address')->nullable()->comment('详细地址');
            $table->smallInteger('status')->comment('状态');
            $table->string('company_name')->nullable()->comment('公司名称');
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
        Schema::dropIfExists('partners');
    }
}
