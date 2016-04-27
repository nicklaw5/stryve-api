<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_channels', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            
            $t->increments('id')->unsigned();
            $t->string('uuid', 36)->unique();
            $t->integer('chat_server_id')->unsigned()->index();             // references chat_server->id
            $t->integer('chat_channel_setting_id')->unsigned()->index();    // references chat_channel_settings->id
            $t->string('name', 100);
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
        Schema::drop('chat_channels');
    }
}
