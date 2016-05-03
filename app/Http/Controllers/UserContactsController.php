<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\User;
use Stryve\Transformers\UsersShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserContactsController extends Controller
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
	 * Returns the users contacts
	 *
	 * @POST("/api/users/contacts")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ { ... } })
	 */
 	public function index(UsersShowTransformer $tranformer)
 	{
 		$response = $tranformer->transformCollection($this->request->user->contacts->toArray());
 		return Larapi::respondOk($response);
 	}

	/**
	 * Creates a new user event
	 *
	 * @POST("/api/users/events/{uuid}")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Request({
	 *		"event_type": 	"user_message",
	 *		"event_text": 	"I like my eggs boiled :P",
	 *		"publish_to": 	"both",
	 *		"editable":		"true"
	 *	})
	 * @Response(200, body={ ... })
	 */
	public function store(UsersShowTransformer $transformer, $uuid)
	{
		
	}
}
