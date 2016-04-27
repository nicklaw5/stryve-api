<?php

namespace Stryve\Transformers;

class ServerEventsShowTransformer extends Transformer
{
	/**
	 * Transform the event for response purposes
	 * 
	 * @param array
	 * @return array
	 */
	public function transform($event)
	{
		return [
			'uuid' 				=> $event['uuid'],
			'server_uuid'		=> $event['chat_server']['uuid'],
			'server_name'		=> $event['chat_server']['name'],
			'owner_uuid'		=> $event['owner']['uuid'],
			'owner_username'	=> $event['owner']['username'],
			'event_type'		=> $event['event_type'],
			'event_text'		=> $event['event_text'],
			'publish_to'		=> $event['publish_to'],
			'created_at'		=> $event['created_at'],
			'updated_at'		=> $event['updated_at']
		];
	}
}