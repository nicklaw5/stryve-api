<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatChannelSetting extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'chat_channel_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the chat channel that owns the setting.
     *
     * @return \App\Models\ChatChannel
     */
    public function chat_channel()
    {
        return $this->belongsTo('App\Models\ChatChannel');
    }

    /**
     * Creates a new channel setting
     * 
     * @param int $channel_id
     * @return this
     */
    public function createChannelSetting($channel_id, $private = false)
    {
        $channel_setting = new $this;
        $channel_setting->chat_channel_id = $channel_id;
        $channel_setting->private = $private;
        $channel_setting->save();

        return $channel_setting;
    }

}