<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServerSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_settings', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            
            $t->increments('id')->unsigned();
            $t->integer('server_id')->unsigned()->index();          // references server->id
            $t->boolean('private')->default(false);                 // public or private server
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
        Schema::drop('server_settings');
    }
}
