<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('用户编码');
            $table->string('username')->nullable()->comment('用户名');
            $table->string('password')->nullable()->comment('密码');
            $table->string('phone')->nullable()->comment('电话');
            $table->string('email')->nullable()->comment('邮箱');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('nickname')->nullable()->comment('昵称');
            $table->smallInteger('sex')->nullable()->comment('性别');
            $table->smallInteger('status')->comment('状态');
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
        Schema::dropIfExists('users');
    }
}
