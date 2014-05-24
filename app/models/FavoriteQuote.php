<?php

class FavoriteQuote extends Eloquent {
	protected $table = 'favorite_quotes';

	protected $fillable = [];

	/**
	 * The validation rules when adding a favorite quote
	 * @var array
	 */
	public static $rulesAddFavorite = [
		'quote_id' => 'required|exists:quotes,id',
		'user_id' => 'required|exists:users,id',
	];

	/**
	 * The validation rules when deleting a favorite quote
	 * @var array
	 */
	public static $rulesRemoveFavorite = [
		'quote_id' => 'required|exists:quotes,id|exists:favorite_quotes,quote_id',
		'user_id' => 'required|exists:users,id|exists:favorite_quotes,user_id',
	];

	public static $cacheNameFavoritesForUser = 'favorites_quotes_for_user_';

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}

	/**
	 * Get the FavoriteQuote for the current user
	 * @throws NotAllowedException when calling this when the visitor is not logged in
	 * @param  $query
	 * @return query object
	 */
	public function scopeCurrentUser($query)
	{
		if (!Auth::check())
			throw new NotAllowedException("Can't get favorites quotes for a guest user!");

		return $query->where('user_id', '=', Auth::id());
	}

	public function scopeForUser($query, $user)
	{
		if (is_numeric($user)) {
			$user_id = (int) $user;
			$user = User::where('id', '=', $user_id)->first();
		}

		return $query->where('user_id', '=', $user->id);
	}
}