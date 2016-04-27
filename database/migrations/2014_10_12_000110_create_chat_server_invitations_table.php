<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatServerInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_server_invitations', function (Blueprint $t) {
            $t->increments('id')->unsigned();
            $t->string('uuid', 36)->unique();
            $t->integer('chat_server_id')->unsigned()->index();     // references chat_server->id
            $t->integer('inviter_id')->unsigned()->index();         // references user->id
            $t->string('token', 16)->unique();
            $t->integer('max_age')->unsigned()->default(86400)->nullable();
            $t->boolean('revoked')->default(false);
            $t->integer('uses')->unsigned()->default(0);
            $t->integer('max_uses')->unsigned()->default(999);
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
        Schema::drop('chat_server_invitations');
    }
}
