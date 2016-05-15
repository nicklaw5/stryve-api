<?php

namespace Stryve\Transformers;

class ContactShowTransformer extends Transformer
{
	/**
	 * Transform the contact for response purposes
	 * 
	 * @param array $contact
	 * @return array
	 */
	public function transform($contact)
	{
		$response = [
			'uuid' 			=> $contact['uuid'],
			'username'		=> $contact['username'],
			'avatar'		=> $contact['avatar'],
			'status'		=> $contact['status'],
			'verified'		=> (boolean) $contact['verified'],
			'created_at'	=> $contact['created_at'],
			'updated_at'	=> $contact['updated_at']
		];

		if(isset($contact['is_contact']))
			$response['is_contact'] = $contact['is_contact'];

		return $response;
	}
}
