<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('编码');
            $table->string('user_code')->comment('用户编码');
            $table->string('parent_code')->nullable()->comment('上级合伙人编码');
            $table->string('name')->comment('名称');
            $table->string('phone')->comment('电话');
            $table->string('province_code')->nullable()->comment('省编码');
            $table->string('province_text')->comment('省中文');
            $table->string('city_code')->nullable()->comment('市编码');
            $table->string('city_text')->comment('市中文');
            $table->string('county_code')->nullable()->comment('县编码');
            $table->string('county_text')->comment('县中文');
            $table->string('address')->nullable()->comment('详细地址');
            $table->dateTime('audit_at')->nullable()->comment('审核时间');
            $table->text('audit_reason')->nullable()->comment('审核原因');
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
        Schema::dropIfExists('partner_applications');
    }
}
