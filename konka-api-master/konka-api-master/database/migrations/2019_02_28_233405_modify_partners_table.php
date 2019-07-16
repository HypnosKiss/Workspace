<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('user_code')->nullable()->change();
            $table->renameColumn('name', 'client_name');
            $table->renameColumn('phone', 'client_phone');
            $table->smallInteger('type')->default(20)->comment('一类、二类合伙人');
            $table->string('area')->nullable()->comment('区域');
            $table->string('company_name')->nullable()->comment('分公司')->change();
            $table->string('invite_code')->comment('邀请码');
            $table->string('activation_code')->nullable()->comment('激活码');
            $table->string('r3_code')->nullable()->comment('R3编码');
            $table->string('merge_code')->nullable()->comment('合并编码');
            $table->string('client_type')->nullable()->comment('客户类型');
            $table->string('company_address')->nullable()->comment('公司地址');
            $table->string('salesman')->nullable()->comment('业务员');
            $table->string('salesman_phone')->nullable()->comment('业务员电话');
            $table->dropColumn('parent_name');
            $table->dropColumn('parent_phone');
            $table->dropColumn('level');
            $table->dropColumn('province_code');
            $table->dropColumn('province_text');
            $table->dropColumn('city_code');
            $table->dropColumn('city_text');
            $table->dropColumn('county_code');
            $table->dropColumn('county_text');
            $table->dropColumn('address');
            $table->dropColumn('points');
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
