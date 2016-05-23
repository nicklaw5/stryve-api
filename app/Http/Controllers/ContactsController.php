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
		$query = isset($this->request->q)? trim($this->request->q) : null;

		// get and restrict the number of events returned
		$limit = isset($this->request->limit)? intval($this->request->limit) : 25;
		$limit = ($limit > 50)? 50 : $limit;

		// search for contacts that are apart of the same servers as the user
		// TODO

		$searchContacts = $this->user
						->where('username', 'LIKE', $query . '%')
						->whereNotIn('id', [$this->request->user->id])
						->limit($limit)
						->get();

		// filter with current contacts to get 'is_contact' attribute
		$existingCotacts = $this->request->user->contacts;
		for($i = 0; $i < count($searchContacts); $i++)
		{
			$isContact = false;
			foreach ($existingCotacts as $exContact)
			{
				if($exContact->id === $searchContacts[$i]->id)
					$isContact = true;
			}

			$searchContacts[$i]->is_contact = $isContact;
		}

		$response = $tranformer->transformCollection($searchContacts->toArray());
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
	 * @POST("/api/contacts/{uuid}")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(201, body={ ... })
	 */
	public function store(ContactShowTransformer $transformer, $uuid)
	{
		$handled = false;
		$isContact = false;

		// get the contact resource
		$contact = $this->user->getUser($uuid);

		// check the contact relationship doesn't already exist
		$contacts = $this->request->user->contacts;
		foreach ($contacts as $exContact)
		{
			if($exContact->id === $contact->id)
			{
				// remove contact relationship
				$this->request->user->contacts()->detach($contact->id);
				$contact->is_contact = false;
				$handled = true;
				break;
			}
		}

		// create contact relationship
		if(!$handled)
		{
			$this->request->user->contacts()->attach($contact->id);
			$contact->is_contact = true;
		}

		// set the response
		// $res = [
		// 	'isContact' => $isContact,
		// 	'contact' => $transformer->transformCollection([$contact->toArray()])[0]
		// ];

		$contact = $transformer->transformCollection([$contact->toArray()])[0];

		return Larapi::respondCreated($contact);
	}
}
