<?php namespace TeenQuotes\Comments\Models;

use Illuminate\Support\Facades\Auth;
use Laracasts\Presenter\PresentableTrait;
use TeenQuotes\Comments\Models\Relations\CommentTrait as CommentRelationsTrait;
use TeenQuotes\Comments\Models\Scopes\CommentTrait as CommentScopesTrait;
use Toloquent;
use TeenQuotes\Users\Models\User;

class Comment extends Toloquent {

	use CommentRelationsTrait, CommentScopesTrait, PresentableTrait;
	protected $presenter = 'TeenQuotes\Comments\Presenters\CommentPresenter';
	
	protected $fillable = [];

	protected $hidden = ['deleted_at', 'updated_at'];

	/**
	 * The validation rules when adding a comment
	 * @var array
	 */
	public static $rulesAdd = [
		'content'  => 'required|min:10|max:500',
		'quote_id' => 'required|exists:quotes,id',
	];

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