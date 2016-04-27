<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatChannel extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'chat_server_id', 'chat_channel_setting_id', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the server that owns the channel.
     *
     * @return \App\Models\ChatServer
     */
    public function chat_server()
    {
        return $this->belongsTo('App\Models\ChatServer');
    }

    /**
     * Get the events for the channel.
     *
     * @return \App\Models\ChatChannelEvent
     */
    public function channel_events()
    {
        return $this->hasMany('App\Models\ChatChannelEvent');
    }

    /**
     * Get the channel settings for the channel.
     *
     * @return \App\Models\ChatChannelSetting
     */
    public function channel_settings()
    {
        return $this->hasOne('App\Models\ChatChannelSetting');
    }

    /**
     * Returns a chat server with associated relationships
     * 
     * @param mixed $id
     * @return this
     */
    public function getChatChannel($id)
    {
        // get server by uuid
        if(strlen(Uuid::import($id)->string) === 36)
            return $this->where('uuid', $id)->with('channel_settings', 'chat_server')->first();

        // get server by id
        return $this->with('channel_settings', 'chat_server')->find($id);
    }

    /**
     * Creates a new chat channel
     * 
     * @param string $name
     * @return this
     */
    public function createNewChatChannel($name, $server_id)
    {
        $channel = new $this;
        $channel->uuid              = Uuid::generate()->string;
        $channel->chat_server_id    = $server_id;
        $channel->name              = $name;
        $channel->save();

        return $channel;
    }

    /**
     * Determines whether or not the provided string can be valid channel name.
     * (aphanumeric, spaces, underscores and hyphens)
     *
     * @param string $string
     * @return bool
     */
    public function isValidChannelName($string)
    {
        // The regular expression for allowable characters in a subdomain: a-Z, 0-9, and hypens (no hypens/underscores at start or end)
        $pattern = '/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9_ ]))*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/i';
        if(1 !== preg_match($pattern, $string))
            return false;
        return true;
    }
}