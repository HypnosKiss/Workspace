<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerServiceMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_service_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('type')->comment('类型');
            $table->smallInteger('message_type')->nullable()->comment('内容类型');
            $table->text('content')->comment('内容');
            $table->string('user_code')->comment('创建人编码');
            $table->string('admin_code')->nullable()->comment('回复人编码');
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
        Schema::dropIfExists('customer_service_messages');
    }
}
