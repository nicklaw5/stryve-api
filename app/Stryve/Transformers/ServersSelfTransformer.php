<?php

namespace Stryve\Transformers;

class ServersSelfTransformer extends Transformer
{
	/**
	 * Transform the server for response purposes
	 * 
	 * @param array
	 * @return array
	 */
	public function transform($server)
	{
		return [
			'uuid' 			=> $server['uuid'],
			'name'			=> $server['name'],
			'avatar'		=> $server['avatar'],
			'created_at'	=> $server['created_at'],
			'updated_at'	=> $server['updated_at']
		];
	}
}