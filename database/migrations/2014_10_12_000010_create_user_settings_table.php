<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_settings', function (Blueprint $t) {
			$t->engine = 'InnoDB';
			
			$t->increments('id')->unsigned();
			$t->integer('user_id')->unsigned()->index();
			$t->string('last_server', 36)->nullable();
			$t->string('last_channel', 36)->nullable();
			$t->enum('theme', ['light', 'dark'])->default('dark');
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
		Schema::drop('user_settings');
	}
}