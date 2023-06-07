<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('openid', 200)->nullable()->default('');
            $table->string('sex', 20)->nullable()->default('未知')->comment('性别 0未知 1男2女');
            $table->string('nickname', 191)->nullable()->comment('昵称');
            $table->string('name', 191)->nullable()->comment('真实名字');
            $table->string('mobile', 100)->nullable()->default('')->comment('手机号');
            $table->string('country', 100)->nullable()->default('');
            $table->string('province', 100)->nullable()->default('');
            $table->string('city', 200)->nullable()->default('');
            $table->string('headimgurl')->nullable();
            $table->string('username', 100)->nullable()->default('');
            $table->string('password', 200)->nullable()->default('');
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
