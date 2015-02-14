<?php namespace TeenQuotes\Comments\Models;

use Auth, Toloquent;
use Laracasts\Presenter\PresentableTrait;
use TeenQuotes\Comments\Models\Relations\CommentTrait as CommentRelationsTrait;
use TeenQuotes\Comments\Models\Scopes\CommentTrait as CommentScopesTrait;
use TeenQuotes\Users\Models\User;

class Comment extends Toloquent {

	use CommentRelationsTrait, CommentScopesTrait, PresentableTrait;
	protected $presenter = 'TeenQuotes\Comments\Presenters\CommentPresenter';

	protected $fillable = [];

	protected $hidden = ['deleted_at', 'updated_at'];

	public function isPostedBySelf()
	{
		if (Auth::check())
			return $this->user_id == Auth::id();

		return false;
	}

	public function isPostedByUser(User $u)
	{
		return $this->user_id == $u->id;
	}
}