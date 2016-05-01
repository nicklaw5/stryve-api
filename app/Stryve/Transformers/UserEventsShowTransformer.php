<?php

namespace Stryve\Transformers;

class UserEventsShowTransformer extends Transformer
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
			'uuid' 					=> $event['uuid'],
			'sender_uuid'			=> $event['sender']['uuid'],
			'sender_username'		=> $event['sender']['username'],
			'recipient_uuid'		=> $event['recipient']['uuid'],
			'recipient_username'	=> $event['recipient']['username'],
			'event_type'			=> $event['event_type'],
			'event_text'			=> $event['event_text'],
			'publish_to'			=> $event['publish_to'],
			'editable'				=> (boolean) $event['editable'],
			'created_at'			=> $event['created_at'],
			'updated_at'			=> $event['updated_at']
		];
	}
}