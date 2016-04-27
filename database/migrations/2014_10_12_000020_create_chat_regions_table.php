<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_regions', function (Blueprint $t) {
            $t->engine = 'InnoDB';
            
            $t->increments('id')->unsigned();
            $t->string('uuid', 36)->unique();
            $t->string('name', 20)->unique();
            $t->string('location')->index(); // sydney, melbourne, us west, us east
            $t->string('server_ip');
            $t->string('server_uri');
            $t->boolean('active')->index();
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
        Schema::drop('chat_regions');
    }
}
