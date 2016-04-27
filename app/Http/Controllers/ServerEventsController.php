<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\ChatServer;
use App\Models\ChatServerEvent;
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
	 * @var \App\Models\ChatServer
	 */
	protected $chat_server;

	/**
	 * @var \App\Models\ChatServerEvent
	 */
	protected $server_event;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Request $request, ChatServer $chat_server, ChatServerEvent $server_event)
	{
		$this->request = $request;
		$this->chat_server = $chat_server;
		$this->server_event = $server_event;
	}

	/**
     * Returns a list of chat channel events for a given channel
     *
     * @GET("/api/channels/{uuid}/events")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @QueryParams({
     *      @Parameter("limit=10", description="The number of events to return. Defaults to 10. Max is 50")
     * })
     * @Response(200, body={ ... })
     */
  //   public function show(ChannelEventsShowTransformer $transformer, $uuid)
  //   {
		// $belongs_to_channel = false;

		// // get the channel
		// $channel = $this->chat_channel->getChatChannel($uuid);

		// // confirm channel exists
		// if(!$channel)
		// 	return Larapi::respondNotFound(config('errors.4041'), 4041);

		// // check the channel belongs to a server that the user belongs to
		// foreach ($this->request->user->chat_servers as $server)
		// {
		// 	if($server->uuid === $channel->chat_server->uuid)
		// 	{
		// 		$belongs_to_channel = true;
		// 		break;
		// 	}
		// }

		// // return unauthorized if user doesnt belong to this channel that they are posting in
		// if(!$belongs_to_channel)
		// 	return Larapi::respondUnauthorized();

		// // get and restrist the number of events returned
		// $limit = empty($this->request->limit)? 10 : intval($this->request->limit);
		// $limit = ($limit > 50)? 50 : $limit;

		// // get the events
		// $events = $this->channel_event->with('chat_channel', 'owner')
		// 							  ->where('chat_channel_id', $channel->id)
		// 							  ->orderBy('created_at', 'desc')
		// 							  ->limit($limit)
		// 							  ->get();

		// // prepare and send response
  //       $response = $transformer->transformCollection($events->toArray());
  //       return Larapi::respondOk($response);
  //   }

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
		$server = $this->chat_server->getChatServer($uuid);

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
        $response = $transformer->transformCollection([$this->server_event->getChatServerEvent($event->id)->toArray()]);
        return Larapi::respondCreated($response[0]);
	}
}
