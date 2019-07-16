<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerServiceClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_service_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('会话编码');
            $table->string('user_code')->comment('用户编码');
            $table->smallInteger('message_type')->comment('消息类型');
            $table->text('last_message')->comment('最后一条消息内容');
            $table->smallInteger('status')->default(10)->comment('会话状态');
            $table->dateTime('last_send_at')->comment('最后消息发送时间');
            $table->smallInteger('unread_num')->default(0)->comment('未读消息数量');
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
        Schema::dropIfExists('custom_service_clients');
    }
}
