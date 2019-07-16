<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityCouponAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_coupon_associations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('activity_coupon_code')->comment('活动优惠劵编码');
            $table->string('coupon_code')->comment('优惠劵编码');
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
        Schema::dropIfExists('activity_coupon_associations');
    }
}
