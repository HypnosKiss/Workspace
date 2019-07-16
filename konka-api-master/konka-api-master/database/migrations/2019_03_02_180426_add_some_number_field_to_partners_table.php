<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeNumberFieldToPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->decimal('total_integral')->default(0)->comment('总积分(K币)');
            $table->decimal('lock_integral')->default(0)->comment('冻结积分(K币)');
            $table->decimal('has_withdraw_integral')->default(0)->comment('已提现积分(K币)');
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
