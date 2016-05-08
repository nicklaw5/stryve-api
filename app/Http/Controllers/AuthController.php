<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Larapi;
use Carbon;
use App\Models\User;
use App\Models\UserSetting;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
	/**
	 * @var \App\Models\User
	 */
	protected $user;

	/**
	 * @var \App\Models\UserSetting
	 */
	protected $user_setting;

	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Request $request, User $user, UserSetting $user_setting)
	{
		$this->user = $user;
		$this->request = $request;
		$this->user_setting = $user_setting;
	}
	

 	/**
	 * Log a user in using their registered `email` and `password`.
	 *
	 * @POST("/api/auth/login")
	 * @Versions({"v1"})
	 * @Request({"email": "johndoe@domain.com", "password": "pass123"})
	 * @Response(200, body={"token": "abc123def456", "token_expires": "2016-01-31 08:31:46"})
	 */
 	public function login()
 	{
 		$token = $this->request->headers->get('authorization');
 		// attempt login with access token
 		if($token)
 		{
			// return Larapi::respondOk([$token]);
	        // attempt to find user from access token
	        $user = $this->user->getUserByAccessToken($token);

	        // if we didn't find a user return unauthorized (invalid access token)
	        if(!$user)
	            return Larapi::respondUnauthorized(config('errors.4011'), 4011);
	        
	        // log the user in
	        Auth::login($user);

	        // update user last login info and get new access token and expiration
	       	$token = $this->user->updateUserOnLogin($this->request);

	        return Larapi::respondCreated(['token' => $token]);
 		}

 		// attempt login with email and password
 		$email 		= empty($this->request->email)? 	null: trim($this->request->email);
 		$password 	= empty($this->request->password)? 	null: trim($this->request->password);
 		
 		// confirm required fields have been provided
 		if(!$email || !$password)
 			return Larapi::respondBadRequest(config('errors.4001'), 4001);

 		// attempt login
 		if (!Auth::attempt(['email' => $email, 'password' => $password]))
			return Larapi::respondBadRequest(config('errors.4002'), 4002);

		/** user authenticated successfully **/

		// update user details and get access token
		$token = $this->user->updateUserOnLogin($this->request);

		return Larapi::respondCreated(['token' => $token]);
 	}


 	/**
	 * Logout a user using their access token
	 *
	 * @POST("/api/auth/logout")
	 * @Versions({"v1"})
	 * @Headers({"Authorization": "myUserAccessToken123abc456def"})
	 * @Response(200, body={})
	 */
 	public function logout()
 	{
 		$user = $this->request->user;
		$user->token = null;
		$user->token_expires = null;
		$user->status = 'offline';
		$user->save();

		Auth::logout();

 		return Larapi::respondOk();
 	}


 	/**
	 * Register a new user with a `email`, `password` and `name`
	 *
	 * @POST("/api/auth/register")
	 * @Versions({"v1"})
	 * @Request({"email": "johndoe@domain.com", "password": "pass123", "name": "John Doe"})
	 * @Response(200, body={"token": "abc123def456", "token_expires": "2016-01-31 08:31:46"})
	 */
 	public function register()
 	{
 		$email 		= empty($this->request->email)? 	null: trim($this->request->email);
 		$password 	= empty($this->request->password)? 	null: trim($this->request->password);
 		$username	= empty($this->request->username)? 	null: $this->request->username;

 		// confirm required fields have been provided provided
 		if(!$email || !$password || !$username)
 			return Larapi::respondBadRequest(config('errors.4001'), 4001);

 		// check for valid username
 		$valid = $this->user->isValidUsername($username);
 		if(is_string($valid))
 			return Larapi::respondBadRequest($valid, 4001);

 		// check for a valid email
 		if(!isValidEmailAddress($email))
 			return Larapi::respondBadRequest('Email address is invalid.', 4001);

 		// check password strength
 		$valid = $this->user->isValidPassword($password);
 		if(is_string($valid))
 			return Larapi::respondBadRequest($valid, 4001);

 		// check email address not already in use
 		if($this->user->where('email', $email)->count() > 0)
 			return Larapi::respondBadRequest('Email address already in use.', 4001);

		// create the new user
		$user = $this->user->createNewUser($username, $email, $password, $this->request);

		// create the user's settings
		$user_setting = $this->user_setting->createNewSetting($user->id);

		// add user setting id to user
		$user->user_setting_id = $user_setting->id;
		$user->save();

		// return with HTTP 200 OK along with access token
		return Larapi::respondCreated(['token' => $user->token]);
 	}

}
