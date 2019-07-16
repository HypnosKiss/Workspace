<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_records', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('type')->comment('类型');
            $table->smallInteger('status')->comment('状态');
            $table->string('audit_order_code')->comment('审核单编码');
            $table->text('reason')->nullable()->comment('审核原因');
            $table->string('audit_person_code')->comment('审核人');
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
        Schema::dropIfExists('approval_records');
    }
}
