<?php

namespace App\Transformers;

use App\Discussion;
use App\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class DiscussionTransformer extends TransformerAbstract
{
	protected $availableIncludes = [
		'user',
		'channel',
		'comments',
	];

	public function transform(Discussion $discussion)
	{
		return [
			'id' 				=> $discussion->id,
			'title' 			=> $discussion->title,
			'slug' 				=> $discussion->slug,
			'body' 				=> $discussion->body,
			'posted_at' 		=> $discussion->created_at,
			'comments_count' 	=> $discussion->comments_count,
		];
	}

	public function includeUser(Discussion $discussion)
	{
		return $this->item($discussion->user, new UserTransformer);
	}

	public function includeChannel(Discussion $discussion)
	{
		return $this->item($discussion->channel, new ChannelTransformer);
	}

	public function includeComments(Discussion $discussion)
	{
		return $this->collection($discussion->comments, new CommentTransformer);
	}
}