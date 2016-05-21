<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerEventsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('server_events', function (Blueprint $t) {
			$t->engine = 'InnoDB';
			
			$t->increments('id')->unsigned();
			$t->string('uuid', 36)->unique();
			$t->integer('server_id')->unsigned()->index();          // references server->id
			$t->integer('owner_id')->unsigned()->index();           // references user->id
			$t->enum('event_type', ['user_connected', 'user_disconnected']);
			$t->text('event_text')->nullable();
			 $t->enum('publish_to', ['none', 'self', 'server_and_self', 'server_not_self']);
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
		Schema::drop('server_events');
	}
}
