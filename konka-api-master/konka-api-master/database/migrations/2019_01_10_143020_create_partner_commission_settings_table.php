<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerCommissionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_commission_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('partner_code')->comment('合伙人编码');
            $table->smallInteger('type')->comment('类型');
            $table->decimal('one_level_commission', 12, 2)->nullable()->comment('一级佣金');
            $table->decimal('two_level_commission', 12, 2)->nullable()->comment('二级佣金');
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
        Schema::dropIfExists('partner_commission_settings');
    }
}
