<?php

namespace Stryve\Transformers;

class ServersShowTransformer extends Transformer
{
	/**
	 * Transform the server for response purposes
	 * 
	 * @param array
	 * @return array
	 */
	public function transform($server)
	{
		$response = [
			'uuid' 			=> $server['uuid'],
			'name'			=> $server['name'],
			'avatar'		=> $server['avatar'],
			'owner'			=> $server['owner']['uuid'],
			'region'		=> $server['region']['name'],
			'location'		=> $server['region']['location'],
			// 'server_ip'		=> $server['region']['server_ip'],
			'server_uri'	=> $server['region']['server_uri'],
			'settings'		=> [
				'private'	=> (boolean) $server['server_settings']['private'],
			],
			'created_at'	=> $server['created_at'],
			'updated_at'	=> $server['updated_at'],
		];

		if(isset($server['chat_channels'])) {
			$response['channels'] = $server['chat_channels'];
		}

		return $response;
	}
}