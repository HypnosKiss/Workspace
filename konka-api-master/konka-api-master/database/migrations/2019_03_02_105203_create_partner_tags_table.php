<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('partner_code')->comment('合伙人编号');
            $table->string('tag_code')->comment('标签编号');
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
        Schema::dropIfExists('partner_tags');
    }
}
