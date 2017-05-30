<?php

namespace App\Transformers;

use App\Channel;
use League\Fractal\TransformerAbstract;

class ChannelTransformer extends TransformerAbstract
{
	public function transform(Channel $channel)
	{
		return [
			'id' 			=> $channel->id,
			'name' 			=> $channel->name,
			'slug' 			=> $channel->slug,
			// 'description' 	=> $channel->description,
			// 'color' 		=> $channel->color,
			// 'restricted' 	=> (bool) $channel->is_restricted,
			// 'hidden' 		=> (bool) $channel->is_hidden,
		];
	}
}