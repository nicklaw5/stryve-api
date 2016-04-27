<?php

namespace Stryve\Transformers;

abstract class Transformer
{
	/**
	 * Transforms a collection of items
	 * 
	 * @param array $items
	 * @return array
	 */
	public function transformCollection($items)
	{
		return array_map([$this, 'transform'], $items);
	}

	public abstract function transform($items);
	
}