<?php

namespace Stryve\Transformers;

class UsersSelfTransformer extends Transformer
{
	/**
	 * Transform the user for response purposes
	 * 
	 * @param array $user
	 * @return array
	 */
	public function transform($user)
	{
		return [
			'uuid' 			=> $user['uuid'],
			'username'		=> $user['username'],
			'email'			=> $user['email'],
			'avatar'		=> $user['avatar'],
			'online'		=> (boolean) $user['online'],
			'verified'		=> (boolean) $user['verified'],
			'settings'		=> [
				'last_chat_server' 	=> $user['user_settings']['last_chat_server'],
				'last_chat_channel' => $user['user_settings']['last_chat_channel'],
				'theme' 			=> $user['user_settings']['theme']
			],
			'created_at'	=> $user['created_at'],
			'updated_at'	=> $user['updated_at']
		];
	}
}