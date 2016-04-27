<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_servers', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            
            $t->increments('id')->unsigned();
            $t->string('uuid', 36)->unique();
            $t->integer('chat_server_setting_id')->unsigned()->index();      // references chat_server_settings->id
            $t->integer('owner_id')->unsigned()->index();                    // references user->id
            $t->integer('region_id')->unsigned()->index();                   // references chat_region->id
            $t->string('name', 50);
            $t->string('avatar')->nullable();
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
        Schema::drop('chat_servers');
    }
}
