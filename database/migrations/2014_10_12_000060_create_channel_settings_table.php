<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('channel_settings', function (Blueprint $t) {
			$t->engine = 'InnoDB';
			
			$t->increments('id')->unsigned();
			$t->integer('channel_id')->unsigned()->index();         // references channel->id
			$t->boolean('private')->default(false);                 // public or private channel
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
		Schema::drop('channel_settings');
	}
}
