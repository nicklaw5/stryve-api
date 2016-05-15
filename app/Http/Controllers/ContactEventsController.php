<?php

namespace App\Http\Controllers;

use Auth;
use Larapi;
use Carbon;
use App\Models\User;
use App\Models\ContactEvent;
use Stryve\Transformers\ContactEventsShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactEventsController extends Controller
{
	/**
	 * @var \App\Models\User
	 */
	protected $user;

	/**
	 * @var \App\Models\ContactEvent
	 */
	protected $contact_event;

	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Request $request, User $user, ContactEvent $contact_event)
	{
		$this->user = $user;
		$this->request = $request;
		$this->contact_event = $contact_event;
	}

	/**
	 * Returns the events between two users
	 *
	 * @POST("/api/contacts/{uuid}/events")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ { ... } })
	 */
 	public function index(ContactEventsShowTransformer $tranformer, $uuid)
 	{
 		$limit = $this->request->limit ?? 25;

 		$contact = $this->user->getUser($uuid);

		// check for blocked messages
		// TODO

 		$matchThese = ['sender_id' => $this->request->user->id, 'recipient_id' => $contact->id];
 		$orThese = ['sender_id' => $contact->id, 'recipient_id' => $this->request->user->id];

 		$events = $this->contact_event->where($matchThese)
 									->orWhere($orThese)
 									->with('sender', 'recipient')
 									->orderBy('created_at', 'desc')
 									->limit($limit)
 									->get();

 		$response = $tranformer->transformCollection($events->toArray());
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
	public function store(ContactEventsShowTransformer $transformer, $uuid)
	{
		// check for valid recipient
		if(!$recipient = $this->user->getUser($uuid, ['user_settings']))
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check recipient has not blocked sender
		// TODO
		
		// filter request data
		$event_uuid = trim($this->request->event_uuid) ?? null;
		$event_type = trim($this->request->event_type) ?? null;
		$event_text = trim($this->request->event_text) ?? null;
		$publish_to = trim($this->request->publish_to) ?? null;
		$editable 	= is_true($this->request->editable);

		// check required fields are valid
		if(!$event_uuid || !$event_type || !$publish_to)
			return Larapi::respondBadRequest(config('errors.4001'), 4001);

		// insert new event
		$event = $this->contact_event->insertNewContactEvent($event_uuid, $this->request->user->id, $recipient->id,
																$event_type, $event_text, $publish_to, $editable);

		// prepare and send response
		$event = $this->contact_event->getContactEvent($event->id, ['sender', 'recipient'])->toArray();
        $response = $transformer->transformCollection([$event]);
        return Larapi::respondOk($response[0]);
	}
}
