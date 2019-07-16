<?php

use Illuminate\Database\Migrations\Migration;

class RemoveSomeUnusedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('partner_infos');
        Schema::drop('partner_application_infos');
        Schema::drop('partner_applications');
        Schema::drop('partner_commission_settings');
        Schema::drop('user_bargain_products');
        Schema::drop('activity_coupon_associations');
        Schema::drop('activity_coupons');
        Schema::drop('bargain_products');
        Schema::drop('bargain_records');
        Schema::drop('coupon_products');
        Schema::drop('coupon_users');
        Schema::drop('order_bargains');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
