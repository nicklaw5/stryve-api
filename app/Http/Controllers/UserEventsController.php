<?php

namespace App\Http\Controllers;

use Auth;
use Larapi;
use Carbon;
use App\Models\User;
use App\Models\UserEvent;
use Stryve\Transformers\UserEventsShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserEventsController extends Controller
{
	/**
	 * @var \App\Models\User
	 */
	protected $user;

	/**
	 * @var \App\Models\UserEvent
	 */
	protected $user_event;

	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Request $request, User $user, UserEvent $user_event)
	{
		$this->user = $user;
		$this->request = $request;
		$this->user_event = $user_event;
	}

 	/**
	 * Returns the events between two users
	 *
	 * @POST("/api/users/events/{uuid}")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ { ... } })
	 */
 	// public function index(UserEventsShowTransformer $tranformer, $uuid)
 	// {

 	// 	$response = $tranformer->transformCollection([$this->request->user->toArray()]);
 	// 	return Larapi::respondOk($response[0]);
 	// }

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
	public function store(UserEventsShowTransformer $transformer, $uuid)
	{
		// check for valid recipient
		if(!$recipient = $this->user->getUser($uuid, ['user_settings']))
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check recipient has not blocked sender
		// TODO
		
		// filter request data
		$event_uuid = trim($this->request->uuid) ?? null;
		$event_type = trim($this->request->event_type) ?? null;
		$event_text = trim($this->request->event_text) ?? null;
		$publish_to = trim($this->request->publish_to) ?? null;
		$editable 	= is_true($this->request->editable);

		// check required fields are valid
		if(!$event_uuid || !$event_type || !$publish_to)
			return Larapi::respondBadRequest(config('errors.4001'), 4001);

		// insert new event
		$event = $this->user_event->insertNewEvent($event_uuid, $this->request->user->id, $recipient->id, $event_type, $event_text, $publish_to, $editable);

		dd($event);

		// prepare and send response
        $response = $transformer->transformCollection([$this->channel_event->getChatChannelEvent($event->id)->toArray()]);
        return Larapi::respondOk($response[0]);
	}
}
