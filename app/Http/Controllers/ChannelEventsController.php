<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\ChatChannel;
use App\Models\ChatChannelEvent;
use Stryve\Transformers\ChannelEventsShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelEventsController extends Controller
{
	/**
	 * @var \App\Models\ChatChannel
	 */
	protected $chat_channel;

	/**
	 * @var \App\Models\ChatChannelEvent
	 */
	protected $channel_event;

	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(ChatChannel $chat_channel, ChatChannelEvent $channel_event, Request $request)
	{
		$this->request = $request;
		$this->chat_channel = $chat_channel;
		$this->channel_event = $channel_event;
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
    public function show(ChannelEventsShowTransformer $transformer, $uuid)
    {
		$belongs_to_channel = false;

		// get the channel
		$channel = $this->chat_channel->getChatChannel($uuid);

		// confirm channel exists
		if(!$channel)
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check the channel belongs to a server that the user belongs to
		foreach ($this->request->user->chat_servers as $server)
		{
			if($server->uuid === $channel->chat_server->uuid)
			{
				$belongs_to_channel = true;
				break;
			}
		}

		// return unauthorized if user doesnt belong to this channel that they are posting in
		if(!$belongs_to_channel)
			return Larapi::respondUnauthorized();

		// get and restrist the number of events returned
		$limit = empty($this->request->limit)? 10 : intval($this->request->limit);
		$limit = ($limit > 50)? 50 : $limit;

		// get the events
		$events = $this->channel_event->with('chat_channel', 'owner')
									  ->where('chat_channel_id', $channel->id)
									  ->orderBy('created_at', 'desc')
									  ->limit($limit)
									  ->get();

		// prepare and send response
        $response = $transformer->transformCollection($events->toArray());
        return Larapi::respondOk($response);
    }

	/**
	 * Creates a new channel event
	 *
	 * @POST("/api/channels/{uuid}/events")
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
	public function store(ChannelEventsShowTransformer $transformer, $uuid)
	{
		$belongs_to_channel = false;

		// get the channel
		$channel = $this->chat_channel->getChatChannel($uuid);

		// confirm channel exists
		if(!$channel)
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check the channel belongs to a server that the user belongs to
		foreach ($this->request->user->chat_servers as $server)
		{
			if($server->uuid === $channel->chat_server->uuid)
			{
				$belongs_to_channel = true;
				break;
			}
		}

		// return unauthorized if user doesnt belong to this channel that they are posting in
		if(!$belongs_to_channel)
			return Larapi::respondUnauthorized();
		
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
		$event = $this->channel_event->insertNewEvent($channel->id, $this->request->user->id, $event_uuid, $event_type, $event_text, $publish_to, $editable);

		// prepare and send response
        $response = $transformer->transformCollection([$this->channel_event->getChatChannelEvent($event->id)->toArray()]);
        return Larapi::respondOk($response[0]);
	}
}
