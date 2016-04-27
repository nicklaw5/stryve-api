<?php

namespace Stryve\Transformers;

class ServerInvitationsShowTransformer extends Transformer
{
	/**
	 * Transform the invitation for response purposes
	 * 
	 * @param array
	 * @return array
	 */
	public function transform($invitation)
	{
		return [
			'uuid' 					=> $invitation['uuid'],
			'server_uuid'			=> $invitation['chat_server']['uuid'],
			'server_name'			=> $invitation['chat_server']['name'],
			'inviter_uuid'			=> $invitation['inviter']['uuid'],
			'inviter_username'		=> $invitation['inviter']['username'],
			'invitation_token'		=> $invitation['token'],
			'invitation_revoked'	=> (boolean) $invitation['revoked'],
			'invitation_uses'		=> $invitation['uses'],
			'invitation_max_uses'	=> $invitation['max_uses'],
			'created_at'			=> $invitation['created_at']
		];
	}
}