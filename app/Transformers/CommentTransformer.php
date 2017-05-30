<?php

namespace App\Transformers;

use App\Comment;
use App\Transformers\UserTransformer;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
	protected $defaultIncludes = [
		'user',
	];
	
	public function transform(Comment $comment)
	{
		return [
			'id' 			=> $comment->id,
			'body' 			=> $comment->content,
			'posted_at' 	=> $comment->created_at,
		];
	}

	public function includeUser(Comment $comment)
	{
		return $this->item($comment->user, new UserTransformer);
	}
}