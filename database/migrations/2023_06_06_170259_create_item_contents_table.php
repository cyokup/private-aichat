<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->nullable();
            $table->integer('item_id')->nullable();
            $table->string('title', 200)->nullable();
            $table->mediumText('content')->nullable();
            $table->string('path', 200)->nullable()->comment('文件地址');
            $table->string('ext', 20)->nullable()->comment('文件后缀');
            $table->boolean('status')->nullable()->default(false)->comment('状态0未处理 1处理中 2处理完成 3处理失败');
            $table->string('size', 20)->nullable()->comment('大小');
            $table->string('remark', 100)->nullable()->comment('备注');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_contents');
    }
};
