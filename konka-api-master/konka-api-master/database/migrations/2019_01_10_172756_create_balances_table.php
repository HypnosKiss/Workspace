<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_code')->comment('用户编码');
            $table->decimal('total_money', 12, 2)->default(0)->comment('总金额');
            $table->decimal('frozen_money', 12, 2)->default(0)->comment('冻结金额');
            $table->string('currency_code')->nullable()->comment('币种编码');
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
        Schema::dropIfExists('balances');
    }
}
