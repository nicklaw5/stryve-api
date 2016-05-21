<?php

namespace Stryve\Transformers;

class ChannelEventsShowTransformer extends Transformer
{
	/**
	 * Transform the server for response purposes
	 * 
	 * @param array
	 * @return array
	 */
	public function transform($event)
	{
		return [
			'uuid' 				=> $event['uuid'],
			'channel_uuid'		=> $event['channel']['uuid'],
			'owner_uuid'		=> $event['owner']['uuid'],
			'owner_username'	=> $event['owner']['username'],
			'event_type'		=> $event['event_type'],
			'event_text'		=> $event['event_text'],
			'publish_to'		=> $event['publish_to'],
			'editable'			=> (boolean) $event['editable'],
			'created_at'		=> $event['created_at'],
			'updated_at'		=> $event['updated_at']
		];
	}
}