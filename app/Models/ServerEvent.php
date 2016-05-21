<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerEvent extends Model
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */ 
    protected $hidden = ['id', 'deleted_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the server that owns the event.
     *
     * @return \App\Models\Server
     */
    public function server()
    {
        return $this->belongsTo('App\Models\Server');
    }

    /**
     * Get the user that owns the event.
     *
     * @return \App\Models\User
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'owner_id', 'id');
    }

    /**
     * Returns a server event with associated relationships
     * 
     * @param mixed $id
     * @return this
     */
    public function getServerEvent($id)
    {
        // get server by uuid
        if(strlen(Uuid::import($id)->string) === 36)
            return $this->where('uuid', $id)->with('server', 'owner')->first();

        // get server by id
        return $this->with('server', 'owner')->find($id);
    }

    /**
     * Create a new server event
     * 
     * @param int $server_id
     * @param int $owner_id
     * @param string $event_type
     * @param string $event_text
     * @param string $publish_to
     * @return this
     */
    public function insertNewEvent($server_id, $owner_id, $event_type, $event_text, $publish_to)
    {
    	$event = new $this;
    	$event->uuid 				= Uuid::generate()->string;
    	$event->server_id 		= $server_id;
    	$event->owner_id 			= $owner_id;
    	$event->event_type 			= $event_type;
    	$event->event_text 			= $event_text;
    	$event->publish_to 			= $publish_to;
    	$event->save();

    	return $event;
    }
}