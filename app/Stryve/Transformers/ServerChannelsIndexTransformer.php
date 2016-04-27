<?php

namespace Stryve\Transformers;

class ServerChannelsIndexTransformer extends Transformer
{
	/**
	 * Transform the server for response purposes
	 * 
	 * @param array
	 * @return array
	 */
	public function transform($channel)
	{
		return [
			'uuid' 			=> $channel['uuid'],
			'name'			=> $channel['name'],
			'created_at'	=> $channel['created_at'],
			'updated_at'	=> $channel['updated_at'],
		];
	}
}