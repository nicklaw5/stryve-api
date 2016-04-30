<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\ChatServer;
use App\Models\ChatChannel;
use App\Models\ChatChannelSetting;
use Stryve\Transformers\ServerChannelsShowTransformer;
use Stryve\Transformers\ServerChannelsIndexTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServerChannelsController extends Controller
{
    /**
     * @var \App\Models\ChatServer
     */
    protected $chat_server;

    /**
     * @var \App\Models\ChatChannel
     */
    protected $chat_channel;

    /**
     * @var \App\Models\ChatChannelSetting
     */
    protected $chat_channel_setting;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Instantiate a new instance
     */
    public function __construct(Request $request, ChatServer $chat_server, ChatChannel $chat_channel,
    							ChatChannelSetting $chat_channel_setting)
    {
    	$this->request = $request;
    	$this->chat_server = $chat_server;
    	$this->chat_channel = $chat_channel;
    	$this->chat_channel_setting = $chat_channel_setting;
    }


    /**
     * Return all the channels that belong to this server
     *
     * @GET("/api/servers/{uuid}/channels")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @PathParams({
     *      @Parameter("uuid", description="The servers unique identifier")
     * })
     * @Response(200, body={...})
     */
    public function index(ServerChannelsIndexTransformer $transformer, $uuid)
    {
    	// get the server
    	$server = $this->chat_server->where('uuid', $uuid)->with('chat_channels')->first();

    	// check we found a server
    	if(!$server)
    		return Larapi::respondNotFound(config('errors.4041'), 4041);

    	// prepare and send response
		$response = $transformer->transformCollection($server->chat_channels->toArray());
		return Larapi::respondOk($response);
    }

    /**
	 * Creates a new server channel
	 *
	 * @POST("/api/servers/{uuid}/channels")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Request({"name": "My Channel"})
	 * @Response(200, body={ ... })
	 */
    public function store(ServerChannelsShowTransformer $transformer, $uuid)
    {
        $channel_name = empty($this->request->name)? null : trim($this->request->name);
        $channel_private = empty($this->request->private)?   false : true;

    	// confirm all required fields have been provided
    	if(!$channel_name)
    		return Larapi::respondBadRequest(config('errors.4001'), 4001);

        // get chat server
        $server = $this->chat_server->getChatServer($uuid);

        // confirm server exists
        if(!$server)
            return Larapi::respondNotFound(config('errors.4041'), 4041);

        // check user is the owner of the server
        if($this->request->user->uuid !== $server->owner->uuid)
            return Larapi::respondUnauthorized();

    	// check the channel name is between 2 and 100 characters in length
    	$len = strlen($channel_name);
    	if($len < 2 || $len > 100)
			return Larapi::respondBadRequest(config('errors.4007'), 4007);

        // check channel name doesnt conflict with an existing channels on this server
        $i = 1;
        foreach ($server->chat_channels as $channel)
            if(strtolower($channel->name) === strtolower($channel_name))
                $channel_name = $channel_name . ' ' . ++$i;

		// create the new channel
		$channel = $this->chat_channel->createNewChatChannel($channel_name, $server->id);

        // create channel setting
        $channel_setting = $this->chat_channel_setting->createChannelSetting($channel->id, $channel_private);

        // update channel with channel_settings_id
        $channel->chat_channel_setting_id = $channel_setting->id;
        $channel->save();

        // prepare and send response
        $response = $transformer->transformCollection([$this->chat_channel->getChatChannel($channel->id)->toArray()]);
        return Larapi::respondCreated($response[0]);
    }
    
}
