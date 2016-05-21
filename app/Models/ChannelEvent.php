<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChannelEvent extends Model
{
	use SoftDeletes;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */ 
	protected $hidden = ['id', 'channel_id', 'owner_id', 'deleted_at'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get the channel that owns the event.
	 *
	 * @return \App\Models\Channel
	 */
	public function channel()
	{
		return $this->belongsTo('App\Models\Channel');
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
	 * Returns a channel event with associated relationships
	 * 
	 * @param mixed $id
	 * @return this
	 */
	public function getChannelEvent($id)
	{
		// get server by uuid
		if(strlen(Uuid::import($id)->string) === 36)
			return $this->where('uuid', $id)->with('channel', 'owner')->first();

		// get server by id
		return $this->with('channel', 'owner')->find($id);
	}

	/**
	 * Create a new channel event
	 * 
	 * @param int $channel_id
	 * @param int $owner_id
	 * @param string $event_uuid
	 * @param string $event_type
	 * @param string $event_text
	 * @param string $publish_to
	 * @param bool $editable
	 * @return this
	 */
	public function insertNewEvent($channel_id, $owner_id, $event_uuid,
									$event_type, $event_text, $publish_to, $editable)
	{
		$event 	= new $this;
		$event->uuid 			= $event_uuid;
		$event->channel_id		= $channel_id;
		$event->owner_id 		= $owner_id;
		$event->event_type 		= $event_type;
		$event->event_text 		= $event_text;
		$event->publish_to 		= $publish_to;
		$event->editable 		= $editable;
		$event->save();

		return $event;
	}
}