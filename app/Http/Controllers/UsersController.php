<?php

namespace App\Http\Controllers;

use Auth;
use Larapi;
use Carbon;
use App\Models\User;
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
	 * Returns the user's data
	 *
	 * @POST("/api/users/self")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={"user": { ... } })
	 */
 	public function self(UsersSelfTransformer $tranformer)
 	{
 		$response = $tranformer->transformCollection([$this->request->user->toArray()]);
 		return Larapi::respondOk($response[0]);
 	}
 	
}
