<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\Server;
use App\Models\ServerEvent;
use Stryve\Transformers\ServerEventsShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServerEventsController extends Controller
{
	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * @var \App\Models\Server
	 */
	protected $server;

	/**
	 * @var \App\Models\ServerEvent
	 */
	protected $server_event;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Request $request, Server $server, ServerEvent $server_event)
	{
		$this->request = $request;
		$this->server = $server;
		$this->server_event = $server_event;
	}

	/**
	 * Creates a new server event
	 *
	 * @POST("/api/servers/{uuid}/events")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Request({
	 *		"event_type": 	"user_message",
	 *		"event_text": 	"I like my eggs boiled.",
	 *		"publish_to": 	"channel_and_self",
	 *		"editable":		"true"
	 *	})
	 * @Response(200, body={ ... })
	 */
	public function store(ServerEventsShowTransformer $transformer, $uuid)
	{
		// get the server
		$server = $this->server->getServer($uuid);

		// confirm server exists
		if(!$server)
			return Larapi::respondNotFound(config('errors.4041'), 4041);
		
		// filter request data
		$event_type = empty($this->request->event_type)? null : trim($this->request->event_type);
		$event_text = empty($this->request->event_text)? null : trim($this->request->event_text);
		$publish_to = empty($this->request->publish_to)? null : trim($this->request->publish_to);

		// check required fields are valid
		if(!$event_type || !$event_text || !$publish_to)
			return Larapi::respondBadRequest(config('errors.4001'), 4001);

		// insert new event
		$event = $this->server_event->insertNewEvent($server->id, $this->request->user->id, $event_type, $event_text, $publish_to);

		// prepare and send response
        $response = $transformer->transformCollection([$this->server_event->getServerEvent($event->id)->toArray()]);
        return Larapi::respondCreated($response[0]);
	}
}
