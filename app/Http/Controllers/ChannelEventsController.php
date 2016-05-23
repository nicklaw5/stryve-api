<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\Channel;
use App\Models\ChannelEvent;
use Stryve\Transformers\ChannelEventsShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelEventsController extends Controller
{
	/**
	 * @var \App\Models\Channel
	 */
	protected $channel;

	/**
	 * @var \App\Models\ChannelEvent
	 */
	protected $channel_event;

	/**
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(Channel $channel, ChannelEvent $channel_event, Request $request)
	{
		$this->request = $request;
		$this->channel = $channel;
		$this->channel_event = $channel_event;
	}

	/**
     * Returns a list of channel events for a given channel
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
		$channel = $this->channel->getChannel($uuid);

		// confirm channel exists
		if(!$channel)
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check the channel belongs to a server that the user belongs to
		foreach ($this->request->user->servers as $server)
		{
			if($server->uuid === $channel->server->uuid)
			{
				$belongs_to_channel = true;
				break;
			}
		}

		// return unauthorized if user doesnt belong to this channel that they are posting in
		if(!$belongs_to_channel)
			return Larapi::respondUnauthorized();

		// get and restrict the number of events returned
		$limit = isset($this->request->limit)? intval($this->request->limit) : 25;
		$limit = ($limit > 50)? 50 : $limit;

		// get the events
		$events = $this->channel_event->with('channel', 'owner')
									  ->where('channel_id', $channel->id)
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
		$channel = $this->channel->getChannel($uuid);

		// confirm channel exists
		if(!$channel)
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check the channel belongs to a server that the user belongs to
		foreach ($this->request->user->servers as $server)
		{
			if($server->uuid === $channel->server->uuid)
			{
				$belongs_to_channel = true;
				break;
			}
		}

		// return unauthorized if user doesnt belong to this channel that they are posting in
		if(!$belongs_to_channel)
			return Larapi::respondUnauthorized();
		
		// trim request data
    	$this->request->replace(array_map('trim', $this->request->all()));

		// filter request data
		$event_uuid = isset($this->request->uuid)? $this->request->uuid : null;
		$event_type = isset($this->request->event_type)? $this->request->event_type : null;
		$event_text = isset($this->request->event_text)? $this->request->event_text : null;
		$publish_to = isset($this->request->publish_to)? $this->request->publish_to : null;
		$editable 	= is_true($this->request->editable);

		$event_text = ($event_text == '')? null: $event_text;

		// check required fields are valid
		if(!$event_uuid || !$event_type || !$publish_to)
			return Larapi::respondBadRequest(config('errors.4001'), 4001);

		// insert new event
		$event = $this->channel_event->insertNewEvent($channel->id, $this->request->user->id, $event_uuid, $event_type, $event_text, $publish_to, $editable);

		// prepare and send response
        $response = $transformer->transformCollection([$this->channel_event->getChannelEvent($event->id)->toArray()]);
        return Larapi::respondCreated($response[0]);
	}
}
