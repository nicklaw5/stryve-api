<?php

namespace App\Http\Controllers;

use Larapi;
use App\Models\Server;
use App\Models\Region;
use App\Models\ServerSetting;
use Stryve\Transformers\ServersSelfTransformer;
use Stryve\Transformers\ServersShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServersController extends Controller
{
    /**
     * @var \App\Models\Server
     */
    protected $server;

    /**
     * @var \App\Models\ServerSetting
     */
    protected $server_setting;

    /**
     * @var \App\Models\Region
     */
    protected $region;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Instantiate a new instance
     */
    public function __construct(Request $request, Server $server, Region $region,
                                ServerSetting $server_setting)
    {
        $this->request = $request;
        $this->region = $region;
        $this->server = $server;
        $this->server_setting = $server_setting;
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
        $response = $transformer->transformCollection($this->request->user->servers->toArray());
        return Larapi::respondOk($response);
    }


    /**
     * Returns a server
     *
     * @GET("/api/servers/{uuid}")
     * @Versions({"v1"})
     * @Headers({"token": "a_long_access_token"})
     * @QueryParams({
     *      @Parameter("channels=true|false", description="TRUE=Include the channels that belong to the server in the response")
     * })
     * @Response(200, body={ ... })
     */
    public function show(ServersShowTransformer $transformer, $uuid)
    {
        $servers = [];
        $withChannels = is_true($this->request->channels) ? true : false;

        // get all the servers the user belongs to
        foreach($this->request->user->servers as $server)
            $servers[] = $server->uuid;

        // return Unauthorized if user does not belong to the requested server
        if(!in_array($uuid, $servers))
            return Larapi::respondUnauthorized();

        // prepare the response and return the response
        $response = $transformer->transformCollection([$this->server->getServer($uuid, $withChannels)->toArray()]);
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
        // trim request data
        $this->request->replace(array_map('trim', $this->request->all()));

        $server_name    = isset($this->request->name)? $this->request->name : null;
        $server_region  = isset($this->request->region)? $this->request->region : null;
        $server_private = empty($this->request->private)?  false : true;

        // confirm required fields have been provided
        if(!$server_name || !$server_region)
            return Larapi::respondBadRequest(config('errors.4003'), 4003);

        // check server name is of valid length
        if(strlen($server_name > 50))
            return Larapi::respondBadRequest(config('errors.4004'), 4004);

        // check that region exists and is enabled
        $region = $this->region->getRegionByName($server_region);
        if(!$region || !$region->active)
            return Larapi::respondBadRequest(config('errors.4005'), 4005);

        // check server name doesnt conflict with an existing server
        $i = 1;
        foreach ($this->request->user->servers as $server)
            if(strtolower($server->name) === strtolower($server_name))
                $server_name = $server_name . ' ' . ++$i;

        // insert new server
        $server = $this->server->createNewServer($region->id, $this->request->user->id, $server_name);

        // insert new server setting
        $server_setting = $this->server_setting->createServerSetting($server->id, $server_private);

        // update server with server setting
        $server->server_setting_id = $server_setting->id;
        $server->save();

        // add server to server_user pivot table
        $server->users()->attach($this->request->user->id);

        // prepare the response and return the response
        $response = $transformer->transformCollection([$this->server->getServer($server->id)->toArray()]);
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
