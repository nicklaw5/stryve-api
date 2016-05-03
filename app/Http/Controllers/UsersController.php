<?php

namespace App\Http\Controllers;

use Auth;
use Larapi;
use Carbon;
use App\Models\User;
use Stryve\Transformers\UsersShowTransformer;
use Stryve\Transformers\UsersSelfTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UsersController extends Controller
{
	/**
	 * @var \App\Models\User
	 */
	protected $user;

	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Request $request, User $user)
	{
		$this->user = $user;
		$this->request = $request;
	}

	/**
	 * Returns a list of users the user may or may not know
	 *
	 * @GET("/api/users")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ { ... } })
	 */
 	public function index(UsersShowTransformer $tranformer)
 	{
		$query = trim($this->request->q) ?? null;
		// $limit = is_null($this->request->limit)? intval($this->request->limit) : 25;
		$limit = 10;

		// search for contacts that are apart of the same servers as the user
		// TODO

 		$response = $tranformer->transformCollection($this->user->where('username', 'LIKE', $query . '%')->limit($limit)->get()->toArray());
 		return Larapi::respondOk($response);
 	}

 	/**
	 * Returns the user's data
	 *
	 * @POST("/api/users/self")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={"user": { ... } })
	 */
 	public function self(UsersSelfTransformer $tranformer)
 	{
 		$user = $this->user->getUser($this->request->user->id, ['contacts', 'user_settings'])->toArray();

 		$response = $tranformer->transformCollection([$user]);
 		return Larapi::respondOk($response[0]);
 	}
 	
}
