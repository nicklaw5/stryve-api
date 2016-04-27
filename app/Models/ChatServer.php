<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatServer extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'chat_server_setting_id', 'owner_id', 'region_id', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The users that belong to this chat server.
     *
     * @return \App\Models\ChatServer
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    /**
     * Get the owner of the chat server.
     *
     * @return \App\Models\User
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the region of the chat server.
     *
     * @return \App\Models\ChatRegion
     */
    public function region()
    {
        return $this->belongsTo('App\Models\ChatRegion');
    }

    /**
     * Get the channels for the server.
     *
     * @return \App\Models\ChatChannel
     */
    public function chat_channels()
    {
        return $this->hasMany('App\Models\ChatChannel');
    }

    /**
     * Get the server settings for the server.
     *
     * @return \App\Models\ChatServerSetting
     */
    public function server_settings()
    {
        return $this->hasOne('App\Models\ChatServerSetting');
    }
    
    /**
     * Returns a chat server with associated relationships
     * 
     * @param mixed $id
     * @return this
     */
    public function getChatServer($id, $withChannels = false)
    {
        if($withChannels)
        {
            // get server by uuid
            if(strlen(Uuid::import($id)->string) === 36)
                return $this->where('uuid', $id)->with('owner', 'region', 'chat_channels', 'server_settings')->first();

            // get server by id
            return $this->with('owner', 'region', 'chat_channels','server_settings')->find($id);
        }

        // get server by uuid
        if(strlen(Uuid::import($id)->string) === 36)
            return $this->where('uuid', $id)->with('owner', 'region', 'server_settings')->first();

        // get server by id
        return $this->with('owner', 'region', 'server_settings')->find($id);
    }

    /**
     * Creates a new chat server
     *
     * @param string $name
     * @param int $region_id
     * @param int $owner_id
     * @return this
     */
    public function createNewServer($region_id, $owner_id, $name)
    {
        $server = new $this;
        $server->uuid       = Uuid::generate()->string;
        $server->name       = $name;
        $server->owner_id   = $owner_id;
        $server->region_id  = $region_id;
        $server->save();

        return $server;
    }
}