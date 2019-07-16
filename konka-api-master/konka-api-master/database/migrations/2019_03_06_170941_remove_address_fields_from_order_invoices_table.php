<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAddressFieldsFromOrderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            $table->dropColumn('province_code');
            $table->dropColumn('city_code');
            $table->dropColumn('county_code');
            $table->renameColumn('province_text', 'province');
            $table->renameColumn('city_text', 'city');
            $table->renameColumn('county_text', 'county');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_invoices', function (Blueprint $table) {
            //
        });
    }
}
