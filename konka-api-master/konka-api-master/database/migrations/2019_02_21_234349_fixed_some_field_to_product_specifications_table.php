<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixedSomeFieldToProductSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('image')->nullable()->comment('规格组合图片');
            $table->text('combination_code')->nullable()->comment('组合编码');
            $table->decimal('guide_price', 10, 2)->default(0)->comment('指导价');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_specifications', function (Blueprint $table) {
            //
        });
    }
}
