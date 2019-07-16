<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('user_code')->comment('用户编码');
            $table->string('name')->comment('收货人名称');
            $table->string('phone')->comment('收货人电话');
            $table->string('province_code')->nullable()->comment('省编码');
            $table->string('province_text')->comment('省中文');
            $table->string('city_code')->nullable()->comment('市编码');
            $table->string('city_text')->comment('市中文');
            $table->string('county_code')->nullable()->comment('县编码');
            $table->string('county_text')->comment('县中文');
            $table->string('address')->comment('详细地址');
            $table->string('postal_code')->nullable()->comment('邮政编码');
            $table->smallInteger('status')->nullable()->comment('状态');
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
        Schema::dropIfExists('user_addresses');
    }
}
