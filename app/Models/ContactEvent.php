<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEvent extends Model
{
	use SoftDeletes;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */ 
	protected $hidden = ['id', 'sender_id', 'recipient_id', 'deleted_at'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get the user that sent the event.
	 *
	 * @return \App\Models\User
	 */
	public function sender()
	{
		return $this->belongsTo('App\Models\User', 'sender_id', 'id');
	}

	/**
	 * Get the intended recipient of the event.
	 *
	 * @return \App\Models\User
	 */
	public function recipient()
	{
		return $this->belongsTo('App\Models\User', 'recipient_id', 'id');
	}

	/**
	 * Returns a user event with associated relationships
	 * 
	 * @param mixed $identifier
	 * @param array $with (optional)
	 * @return this
	 */
	public function getContactEvent($identifier, $with = [])
	{
		return $this->where(function($query) use ($identifier) {
			$column = strlen(Uuid::import($identifier)->string) === 36 ? 'uuid' : 'id';
			$query->where($column, $identifier);
		})->with($with)->first();
	}

	/**
	 * Create a new user event
	 * 
	 * @param string $event_uuid
	 * @param int $sender_id
	 * @param int $recipient_id
	 * @param string $event_type
	 * @param string $event_text
	 * @param string $publish_to
	 * @param bool $editable
	 * @return this
	 */
	public function insertNewContactEvent($event_uuid, $sender_id, $recipient_id, $event_type, $event_text, $publish_to, $editable)
	{
		$event = new $this;
		$event->uuid 				= $event_uuid;
		$event->sender_id 	        = $sender_id;
		$event->recipient_id		= $recipient_id;
		$event->event_type 			= $event_type;
		$event->event_text 			= $event_text;
		$event->publish_to 			= $publish_to;
		$event->editable 			= $editable;
		$event->save();

		return $event;
	}
}
