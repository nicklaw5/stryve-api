<?php

namespace Stryve\Transformers;

class UsersShowTransformer extends Transformer
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
			'avatar'		=> $user['avatar'],
			'online'		=> (boolean) $user['online'],
			'verified'		=> (boolean) $user['verified'],
			'created_at'	=> $user['created_at'],
			'updated_at'	=> $user['updated_at']
		];
	}
}