<?php

namespace App\Models;

use Auth;
use Hash;
use Uuid;
use Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use SoftDeletes;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */ 
	protected $hidden = [
		'id', 'password', 'token', 'token_expires', 'last_ip', 'deleted_at',
		'last_login', 'verification_token', 'user_setting_id', 'remember_token'
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Get the user settings for the user
	 *
	 * @return \App\Models\UserSetting
	 */
	public function user_settings()
	{
		return $this->hasOne('App\Models\UserSetting');
	}

	/**
	 * Get the contacts for the user
	 *
	 * @return \App\Models\User
	 */
	public function contacts()
	{
		return $this->belongsToMany('App\Models\User', 'contacts', 'user_id', 'contact_id');
	}

	/**
	 * Get the servers that the user is associated with.
	 *
	 * @return \App\Models\Server
	 */
	public function servers()
	{
		return $this->belongsToMany('App\Models\Server');
	}

	/**
	 * Get the servers this user owns.
	 *
	 * @return \App\Models\Server
	 */
	public function owned_servers()
	{
		return $this->hasMany('App\Models\Server', 'owner_id', 'id');
	}

	/**
	 * Get the channel events for the user.
	 *
	 * @return \App\Models\ChannelEvent
	 */
	public function channel_events()
	{
		return $this->hasMany('App\Models\ChannelEvent', 'owner_id', 'id');
	}

	/**
	 * Finds and return a user object via the user's access token
	 * 
	 * @param string $token
	 * @param bool $to_array
	 * @return mixed
	 */
	public function getUserByAccessToken($token, $to_array = false)
	{
		$user = $this->whereRaw('BINARY `token` = ?', [$token])->with('user_settings')->first();

		return $to_array ? $user->toArray() : $user;
	}

	/**
	 * Returns a user with associated relationships
	 * 
	 * @param mixed $identifier
	 * @param array $with (optional)
	 * @return this
	 */
	public function getUser($identifier, $with = [])
	{
		return $this->where(function($query) use ($identifier) {
			$column = (strlen(Uuid::import($identifier)->string) === 36)? 'uuid' : 'id';
			$query->where($column, $identifier);
		})->with($with)->first();
	}

	/**
	 * Inserts a new user
	 * 
	 * @param string $username
	 * @param string $email
	 * @param string $password
	 * @param \Illuminate\Http\Request $request
	 * @return this
	 */
	public function createNewUser($username, $email, $password, $request)
	{
		// generate user access and email verification tokens
		$token = $this->generateNewToken();
		$token_expires = $this->setAccessTokenExpiration();
		$verification_token = $this->generateVerificationToken();

		// insert the user
		$user = new $this;
		$user->uuid                 = Uuid::generate()->string;
		$user->username             = $username;
		$user->email                = strtolower($email);
		$user->password             = Hash::make($password);
		$user->token                = $token;
		$user->token_expires        = $token_expires;
		$user->status               = 'online';
		$user->last_ip              = $request->ip();
		$user->last_login           = Carbon::now();
		$user->verification_token   = $verification_token;
		$user->save();

		return $user;
	}

	/**
	 * Update an existing user upon loging in
	 * 
	 * @param \Illuminate\Http\Request $request
	 * @return string
	 */
	public function updateUserOnLogin($request)
	{
		// generate user access token
		$token = $this->generateNewToken();
		$token_expires = $this->setAccessTokenExpiration();

		// update the database
		$user = $this->find(Auth::user()->id);
		$user->token                = $token;
		$user->token_expires        = $token_expires;
		$user->status               = 'online';
		$user->last_ip              = $request->ip();
		$user->last_login           = Carbon::now();
		$user->save();

		// return the new access token
		return $token;
	}

	/**
	 * Generates a new unique 60 character long acecss token.
	 * 
	 * @return string
	 */
	private function generateNewToken()
	{
		do {
			$token = generateRandomString(60, true, true, true, false);
		} while($this->tokenExists($token, 'token'));

		return $token;
	}

	/**
	 * Sets the access token's expiration date.
	 * 
	 * @return int
	 */
	private function setAccessTokenExpiration()
	{
		return strtotime('+1 week');
	}

	/**
	 * Generates a random and unique 60 character
	 * email verification token.
	 * 
	 * @return string
	 */
	private function generateVerificationToken()
	{
		 do {
			$token = generateRandomString(60, true, true, true, false);
		} while($this->tokenExists($token, 'verification_token'));

		return $token;
	}

	/**
	 * Checks to see if an existing token is already
	 * registered under a given column
	 * 
	 * @param string $token
	 * @return bool
	 */
	private function tokenExists($token, $column)
	{
		if($this->whereRaw("BINARY `$column` = ?", [$token])->count() > 0)
			return true;

		return false;
	}

	/**
	 * Checks the strength of a users password
	 * 
	 * @param string $password
	 * @param int $min
	 * @param int $max
	 * @return mixed
	 */
	function isValidPassword($password, $min = 6, $max = 25) {

		$length = strlen($password);

		if ($length < $min || $length > $max)
			return "Password doesn't meet character length requirements. Password must be between $min and $max characters.";

		if (!preg_match("#[0-9]+#", $password))
		   return "Password must include at least one number.";

		if (!preg_match("#[a-zA-Z]+#", $password))
			return "Password must include at least one letter!";

		return true;
	}

	/**
	 * Checks the validity of a user's username
	 * 
	 * @param string $username
	 * @param int $min
	 * @param int $max
	 * @return mixed
	 */
	function isValidUsername($username, $min = 2, $max = 32) {

		$length = strlen($username);

		if ($length < $min || $length > $max)
			return "Username doesn't meet character length requirements. Username must be between $min and $max characters.";

		if (!preg_match('/^[a-z\d_\s]{2,32}/i', $username))
			return "Username can only contain alpha-numeric, underscore and space characters.";
		
		return true;
	}
}
