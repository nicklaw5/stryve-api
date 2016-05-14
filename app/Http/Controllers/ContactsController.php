<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\User;
use Stryve\Transformers\ContactShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ContactsController extends Controller
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
	 * Queries the database for users belonging to the same servers
	 *
	 * @GET("/api/contacts")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ { ... } })
	 */
 	public function search(ContactShowTransformer $tranformer)
 	{
		$query = trim($this->request->q) ?? null;

		// get and restrict the number of events returned
		$limit = isset($this->request->limit)? intval($this->request->limit) : 25;
		$limit = ($limit > 50)? 50 : $limit;

		// search for contacts that are apart of the same servers as the user
		// TODO

		$contacts = $this->user
						->where('username', 'LIKE', $query . '%')
						->whereNotIn('id', [$this->request->user->id])
						->limit($limit)
						->get()
						->toArray();

 		$response = $tranformer->transformCollection($contacts);
 		return Larapi::respondOk($response);
 	}

 	/**
	 * Returns the users contacts
	 *
	 * @POST("/api/users/contacts")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ { ... } })
	 */
 	public function index(ContactShowTransformer $tranformer)
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
	public function store(ContactShowTransformer $transformer, $uuid)
	{
		
	}
}
