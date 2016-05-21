<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
	use SoftDeletes;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */ 
	protected $hidden = ['id', 'server_id', 'channel_setting_id', 'deleted_at'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get the server that owns the channel.
	 *
	 * @return \App\Models\Server
	 */
	public function server()
	{
		return $this->belongsTo('App\Models\Server');
	}

	/**
	 * Get the events for the channel.
	 *
	 * @return \App\Models\ChannelEvent
	 */
	public function channel_events()
	{
		return $this->hasMany('App\Models\ChannelEvent');
	}

	/**
	 * Get the channel settings for the channel.
	 *
	 * @return \App\Models\ChannelSetting
	 */
	public function channel_settings()
	{
		return $this->hasOne('App\Models\ChannelSetting');
	}

	/**
	 * Returns a server with associated relationships
	 * 
	 * @param mixed $id
	 * @return this
	 */
	public function getChannel($id)
	{
		// get server by uuid
		if(strlen(Uuid::import($id)->string) === 36)
			return $this->where('uuid', $id)->with('channel_settings', 'server')->first();

		// get server by id
		return $this->with('channel_settings', 'server')->find($id);
	}

	/**
	 * Creates a new channel
	 * 
	 * @param string $name
	 * @return this
	 */
	public function createNewChannel($name, $server_id)
	{
		$channel = new $this;
		$channel->uuid              = Uuid::generate()->string;
		$channel->server_id         = $server_id;
		$channel->name              = $name;
		$channel->save();

		return $channel;
	}
}