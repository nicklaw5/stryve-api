<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerSetting extends Model
{
	use SoftDeletes;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */ 
	protected $hidden = ['id', 'server_id', 'created_at', 'updated_at', 'deleted_at'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get the server that owns the setting.
	 *
	 * @return \App\Models\Server
	 */
	public function server()
	{
		return $this->belongsTo('App\Models\Server');
	}

	/**
	 * Create a new server setting
	 * 
	 * @param int $server_id
	 * @param bool $private
	 * @return this
	 */
	public function createServerSetting($server_id, $private)
	{
		$setting = new $this;
		$setting->server_id     = $server_id;
		$setting->private 		= $private;
		$setting->save();

		return $setting;
	}

}