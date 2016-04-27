<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatServerSetting extends Model
{
	use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'chat_server_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the chat server that owns the setting.
     *
     * @return \App\Models\ChatServer
     */
    public function chat_server()
    {
        return $this->belongsTo('App\Models\ChatServer');
    }

    /**
     * Create a new chat server setting
     * 
     * @param int $server_id
     * @param bool $private
     * @return this
     */
    public function createServerSetting($server_id, $private)
    {
        $setting = new $this;
        $setting->chat_server_id    = $server_id;
        $setting->private           = $private;
        $setting->save();

        return $setting;
    }

}