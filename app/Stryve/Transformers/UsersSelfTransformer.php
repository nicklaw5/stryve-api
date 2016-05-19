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
		$response = [
			'uuid' 			=> $user['uuid'],
			'username'		=> $user['username'],
			'email'			=> $user['email'],
			'avatar'		=> $user['avatar'],
			'status'		=> $user['status'],
			'verified'		=> (boolean) $user['verified'],
			'contacts'		=> [],
			'settings'		=> [
				'last_chat_server' 	=> $user['user_settings']['last_chat_server'],
				'last_chat_channel' => $user['user_settings']['last_chat_channel'],
				'theme' 			=> $user['user_settings']['theme']
			],
			'created_at'	=> $user['created_at'],
			'updated_at'	=> $user['updated_at']
		];

		foreach ($user['contacts'] as $contact)
		{
			$response['contacts'][] = [
				'uuid' 			=> $contact['uuid'],
				'username'		=> $contact['username'],
				'avatar'		=> $contact['avatar'],
				'status'		=> $contact['status'],
				'verified'		=> (boolean) $contact['verified'],
				'created_at'	=> $contact['created_at'],
				'updated_at'	=> $contact['updated_at']
			];
		}

		return $response;
	}
}