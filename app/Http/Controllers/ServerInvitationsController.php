<?php

namespace App\Http\Controllers;

use Carbon;
use Larapi;
use App\Models\ChatServer;
use App\Models\ChatServerInvitation;
use Stryve\Transformers\ServersShowTransformer;
use Stryve\Transformers\ServerInvitationsShowTransformer;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServerInvitationsController extends Controller
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
	 * @var \App\Models\ChatServerInvitation
	 */
	protected $chat_server_invitation;

	/**
	 * Instantiate a new instance
	 */
	public function __construct(ChatServer $chat_server, ChatServerInvitation $chat_server_invitation, Request $request)
	{
		$this->request = $request;
		$this->chat_server = $chat_server;
		$this->chat_server_invitation = $chat_server_invitation;
	}

	/**
	 * Accept a server invitation
	 *
	 * @POST("/api/invitations/{token}")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ ... })
	 */
	public function show(ServersShowTransformer $transformer, $token)
	{
		$invitation = $this->chat_server_invitation->whereRaw("BINARY `token` = ?", [$token])->first();

		// check have a valid token and it hasnt been revoked
		if(!$invitation || $invitation->revoked)
			return Larapi::respondBadRequest(config('errors.4008'), 4008);

		// check token hasn't expired
		if(time() > (time() + $invitation->max_age))
			return Larapi::respondBadRequest(config('errors.4009'), 4009);

		// check token hasn't exceeded its max uses
		if($invitation->uses >= $invitation->max_uses)
			return Larapi::respondBadRequest(config('errors.4010'), 4010);


		// get the server that the token represents
		$server = $this->chat_server->getChatServer($invitation->chat_server_id);
		
		// confirm server still exists
		if(!$server)
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check the user doesn't already exists as part of this channel
		foreach($this->request->user->chat_servers as $user_server)
			if($server->id === $user_server->id)
				return Larapi::respondBadRequest(config('errors.4011'), 4011);

		// attach the user as a member of the server
		$server->users()->attach($this->request->user->id);

		// increment token uses
		$invitation->uses += 1;
		$invitation->save();

		// prepare and send response
        $response = $transformer->transformCollection([$this->chat_server->getChatServer($server->id, true)->toArray()]);
        return Larapi::respondOk($response[0]);
	}

	/**
	 * Creates a new server invitation
	 *
	 * @POST("/api/servers/{uuid}/invitations")
	 * @Versions({"v1"})
	 * @Headers({"token": "a_long_access_token"})
	 * @Response(200, body={ ... })
	 */
	public function store(ServerInvitationsShowTransformer $transformer, $uuid)
	{
		// get the server
		$server = $this->chat_server->getChatServer($uuid);
	
		// confirm server exists
		if(!$server)
			return Larapi::respondNotFound(config('errors.4041'), 4041);

		// check the user has permissions to create a new invitation token
		if($server->owner_id !== $this->request->user->id)
			return Larapi::respondUnauthorized(config('errors.4012'), 4012);

		// insert new event
		$invitation = $this->chat_server_invitation->insertNewInvitation($server->id, $this->request->user->id);

		// prepare and send response
        $response = $transformer->transformCollection([$this->chat_server_invitation->getChatServerInvitation($invitation->id)->toArray()]);
        return Larapi::respondCreated($response[0]);
	}
}
