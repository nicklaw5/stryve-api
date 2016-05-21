<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('channels', function (Blueprint $t) {
			$t->engine = 'InnoDB';
			
			$t->increments('id')->unsigned();
			$t->string('uuid', 36)->unique();
			$t->integer('server_id')->unsigned()->index();             // references server->id
			$t->integer('channel_setting_id')->unsigned()->index();    // references channel_settings->id
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
		Schema::drop('channels');
	}
}
