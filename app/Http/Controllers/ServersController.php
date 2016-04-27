<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\ChatServer;
use App\Models\ChatRegion;
use App\Models\ChatServerSetting;
use Stryve\Transformers\ServersSelfTransformer;
use Stryve\Transformers\ServersShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServersController extends Controller
{
    /**
     * @var \App\Models\ChatServer
     */
    protected $chat_server;

    /**
     * @var \App\Models\ChatServerSetting
     */
    protected $chat_server_setting;

    /**
     * @var \App\Models\ChatRegion
     */
    protected $chat_region;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Instantiate a new instance
     */
    public function __construct(Request $request, ChatServer $chat_server, ChatRegion $chat_region,
                                ChatServerSetting $chat_server_setting)
    {
        $this->request = $request;
        $this->chat_region = $chat_region;
        $this->chat_server = $chat_server;
        $this->chat_server_setting = $chat_server_setting;
    }


    /**
     * Returns the servers the user belongs to
     *
     * @GET("/api/servers/self")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @Response(200, body={"servers": { ... } })
     */
    public function self(ServersSelfTransformer $transformer)
    {
        // prepare the response and return the response
        $response = $transformer->transformCollection($this->request->user->chat_servers->toArray());
        return Larapi::respondOk($response);
    }


    /**
     * Returns a server
     *
     * @GET("/api/servers/{uuid}")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @QueryParams({
     *      @Parameter("channels=true|false", description="TRUE=Include the chat channels that belong to the server in the response")
     * })
     * @Response(200, body={ ... })
     */
    public function show(ServersShowTransformer $transformer, $uuid)
    {
        $servers = [];
        $withChannels = is_true($this->request->channels) ? true : false;

        // get all the servers the user belongs to
        foreach($this->request->user->chat_servers as $server)
            $servers[] = $server->uuid;

        // return Unauthorized if user does not belong to the requested server
        if(!in_array($uuid, $servers))
            return Larapi::respondUnauthorized();

        // prepare the response and return the response
        $response = $transformer->transformCollection([$this->chat_server->getChatServer($uuid, $withChannels)->toArray()]);
        return Larapi::respondOk($response[0]);
    }


    /**
     * Creates a new server
     *
     * @POST("/api/servers")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @Request({"server_name": "My Server", "server_region": "melb-01"})
     * @Response(200, body={ ... })
     */
    public function store(ServersShowTransformer $transformer)
    {           
        $server_name    = trim($this->request->name) ?? null;
        $server_region  = trim($this->request->region) ?? null;
        $server_private = empty($this->request->private)?   false : true;

        // confirm required fields have been provided
        if(!$server_name || !$server_region)
            return Larapi::respondBadRequest(config('errors.4003'), 4003);

        // check server name is of valid length
        if(strlen($server_name > 50))
            return Larapi::respondBadRequest(config('errors.4004'), 4004);

        // check that region exists and is enabled
        $chat_region = $this->chat_region->getChatRegionByName($server_region);
        if(!$chat_region || !$chat_region->active)
            return Larapi::respondBadRequest(config('errors.4005'), 4005);

        // check server name doesnt conflict with an existing server
        $i = 1;
        foreach ($this->request->user->chat_servers as $server)
            if(strtolower($server->name) === strtolower($server_name))
                $server_name = $server_name . ' ' . ++$i;

        // insert new server
        $server = $this->chat_server->createNewServer($chat_region->id, $this->request->user->id, $server_name);

        // insert new server setting
        $server_setting = $this->chat_server_setting->createServerSetting($server->id, $server_private);

        // update server with server setting
        $server->chat_server_setting_id = $server_setting->id;
        $server->save();

        // add server to chat_server_user pivot table
        $server->users()->attach($this->request->user->id);

        // prepare the response and return the response
        $response = $transformer->transformCollection([$this->chat_server->getChatServer($server->id)->toArray()]);
        return Larapi::respondCreated($response[0]);
    }


    /**
     * Updates a server
     *
     * @POST("/api/servers/{uuid}")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @PathParams({
     *      @Parameter("uuid", description="The servers unique identifier")
     * })
     * @Response(200, body={"server": { ... } })
     */
    public function update($uuid)
    {
        // TODO:: update server
    }


    /**
     * Deletes a server
     *
     * @POST("/api/servers/{uuid}")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @PathParams({
     *      @Parameter("uuid", description="The servers unique identifier")
     * })
     * @Response(200, body={ })
     */
    public function delete($uuid)
    {
        // TODO:: delete server
    }
}
