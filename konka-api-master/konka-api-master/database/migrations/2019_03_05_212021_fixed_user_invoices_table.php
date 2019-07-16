<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixedUserInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_type');
            $table->dropColumn('name');
            $table->dropColumn('phone');
            $table->dropColumn('province_code');
            $table->dropColumn('province_text');
            $table->dropColumn('city_code');
            $table->dropColumn('city_text');
            $table->dropColumn('county_code');
            $table->dropColumn('county_text');
            $table->dropColumn('address');
            $table->dropColumn('postal_code');
            $table->dropColumn('mobile');
            $table->dropColumn('email');
            $table->renameColumn('status', 'is_default');
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
