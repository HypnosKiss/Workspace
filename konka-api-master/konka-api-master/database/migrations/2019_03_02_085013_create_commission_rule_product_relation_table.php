<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommissionRuleProductRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_rule_product_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('commission_rule_code')->comment('合伙人规则');
            $table->string('product_tag_code')->comment('产品标签编号');
            $table->smallInteger('status')->default(10)->comment('状态');
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
        Schema::dropIfExists('commission_rule_product_relation');
    }
}
