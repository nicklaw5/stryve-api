<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEventsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_events', function (Blueprint $t) {
			$t->engine = 'InnoDB';
			
			$t->increments('id')->unsigned();
			$t->string('uuid', 36)->unique();
			$t->integer('sender_id')->unsigned()->index();         			// references user->id
			$t->integer('recipient_id')->unsigned()->index();           	// references user->id
			$t->enum('event_type', ['user_command', 'user_message', 'user_attachment']);
			$t->text('event_text')->nullable();
			$t->enum('publish_to', ['none', 'sender', 'recipient', 'both']);
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
		Schema::drop('user_events');
	}
}
