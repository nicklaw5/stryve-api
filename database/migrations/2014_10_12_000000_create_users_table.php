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
        Schema::create('users', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            
            $t->increments('id')->unsigned();
            $t->string('uuid', 36)->unique();
            $t->integer('user_setting_id')->unsigned()->index();   // references user_settings
            $t->string('username', 32);
            $t->string('email')->unique();
            $t->string('password', 60);
            $t->string('avatar')->nullable();
            $t->boolean('online')->default(0);
            $t->string('token', 60)->index()->nullable();
            $t->integer('token_expires')->unsigned()->nullable();
            $t->boolean('verified')->default(0);
            $t->string('verification_token', 60)->index()->nullable();
            $t->string('last_ip')->nullable();
            $t->timestamp('last_login')->nullable();
            $t->rememberToken();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
