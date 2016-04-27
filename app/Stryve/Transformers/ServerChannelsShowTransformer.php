<?php

namespace Stryve\Transformers;

class ServerChannelsShowTransformer extends Transformer
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
			'settings'		=> [
				'private'	=> (boolean) $channel['channel_settings']['private'],
			],
			'created_at'	=> $channel['created_at'],
			'updated_at'	=> $channel['updated_at'],
		];
	}
}