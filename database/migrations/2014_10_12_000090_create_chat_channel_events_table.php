<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatChannelEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_channel_events', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            
            $t->increments('id')->unsigned();
            $t->string('uuid', 36)->unique();
            $t->integer('chat_channel_id')->unsigned()->index();    // references chat_channel->id
            $t->integer('owner_id')->unsigned()->index();           // references user->id
            $t->enum('event_type', ['user_command', 'user_message', 'user_attachment', 'user_subscribed', 'user_unsubscribed']);
            $t->text('event_text')->nullable();
            $t->enum('publish_to', ['none', 'self', 'channel_and_self', 'channel_not_self']);
            $t->boolean('editable')->default(true);
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
        Schema::drop('chat_channel_events');
    }
}
