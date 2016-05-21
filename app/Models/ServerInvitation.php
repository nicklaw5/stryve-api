<?php

namespace App\Models;

use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerInvitation extends Model
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
	 * Get the server that owns the invitation.
	 *
	 * @return \App\Models\Server
	 */
	public function server()
	{
		return $this->belongsTo('App\Models\Server');
	}

	/**
	 * Get the user that owns the created the invitation.
	 *
	 * @return \App\Models\User
	 */
	public function inviter()
	{
		return $this->belongsTo('App\Models\User', 'inviter_id', 'id');
	}

	/**
	 * Returns a server invitation with associated relationships
	 * 
	 * @param mixed $id
	 * @return this
	 */
	public function getServerInvitation($id)
	{
		// get server by uuid
		if(strlen(Uuid::import($id)->string) === 36)
			return $this->where('uuid', $id)->with('server', 'inviter')->first();

		// get server by id
		return $this->with('server', 'inviter')->find($id);
	}

	/**
	 * Create a new server invitation
	 * 
	 * @param int $server_id
	 * @param int $inviter_id
	 * @return this
	 */
	public function insertNewInvitation($server_id, $inviter_id)
	{
		$invitation = new $this;
		$invitation->uuid          	= Uuid::generate()->string;
		$invitation->server_id 	  	= $server_id;
		$invitation->inviter_id    	= $inviter_id;
		$invitation->token 		 	= $this->generateInvitationToken();
		$invitation->save();

		return $invitation;
	}

	/**
	 * Generates a random and unique 16 character
	 * invitation token.
	 * 
	 * @return string
	 */
	private function generateInvitationToken($length = 16)
	{
		do {
			$token = generateRandomString($length, true, true, true, false);
		} while($this->tokenExists($token));

		return $token;
	}

	/**
	 * Checks to see if an existing inivitation token is already
	 * registered under a given column
	 * 
	 * @param string $token
	 * @return bool
	 */
	public function tokenExists($token, $column = 'token')
	{
		if($this->whereRaw("BINARY `$column` = ?", [$token])->count() > 0)
			return true;

		return false;
	}

}
