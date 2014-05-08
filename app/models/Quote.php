<?php

class Quote extends Eloquent {
	protected $fillable = [];

	/**
	 * Adding customs attributes to the object
	 * @var array
	 */
	protected $appends = array('has_favorites', 'total_favorites', 'has_comments', 'total_comments');

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rulesAdd = [
		'content'              => 'required|min:50|max:300|unique:quotes,content',
		'quotesSubmittedToday' => 'required|integer|between:0,5',
	];

	/**
	 * The colors that will be used for quotes on the random page / homepage
	 * @var array
	 */
	public static $colors = [
		'#27ae60', '#16a085', '#d35400', '#e74c3c', '#8e44ad', '#F9690E', '#2c3e50', '#f1c40f', '#65C6BB', '#E08283'
	];

	/**
	 * The colors that will be used for quotes on the profile page
	 * @var array
	 */
	public static $colorsProfile = [
		'#5C97BF', '#2574A9', '#3A539B', '#1E8BC3', '#19B5FE', '#89C4F4', '#3498DB', '#52B3D9'
	];

	/**
	 * @brief The name of the key to store in cache. Describes the number of comments for a given quote.
	 * @var string
	 */
	public static $cacheNameNbComments = 'nb_comments_';

	/**
	 * @brief The name of the key to store in cache. Describes the number of favorites for a given quote.
	 * @var string
	 */
	public static $cacheNameNbFavorites = 'nb_favorites_';

	/**
	 * @brief The name of the key to store in cache. Describes the quotes for a given page.
	 * @var string
	 */
	public static $cacheNameQuotesPage = 'quotes_homepage_';

	/**
	 * @brief The name of the key to store in cache. Describes the quotes for a given "random" page.
	 * @var string
	 */
	public static $cacheNameRandomPage = 'quotes_random_';

	/**
	 * @brief The name of the key to store in cache. Describes the number of quotes that have been published.
	 * @var string
	 */
	public static $cacheNameNumberPublished = 'nb_quotes_published';

	public static function getRandomColors($profile = false)
	{
		if (!$profile) {
			if (Session::has('quotesColors') AND !empty(Session::get('quotesColors')))
				return Session::get('quotesColors');
			else {
				$colors = self::$colors;
				shuffle($colors);

				Session::put('quotesColors', $colors);

				return $colors;
			}
		}
		else {
			if (Session::has('quotesColorsProfile') AND !empty(Session::get('quotesColorsProfile')))
				return Session::get('quotesColorsProfile');
			else {
				$colors = self::$colorsProfile;
				shuffle($colors);

				Session::put('quotesColorsProfile', $colors);

				return $colors;
			}
		}
	}

	/**
	 * @brief Return the text for the Twitter Card. Displayed on single quote page.
	 * @return The text for the Twitter Card
	 */
	public function textTwitterCard()
	{
		$content = $this->content;
		$maxLength = 180;

		if (strlen($content) > $maxLength)  {
			$content = substr($content, 0, $maxLength);
			$lastSpace = strrpos($content, " ");

			// After the space, add …
			$content = substr($content, 0, $lastSpace).'…';
		}

		return $content;
	}

	/**
	 * @brief The text that will be tweeted. Given to the Twitter sharer.
	 * @return The text for the Twitter sharer
	 */
	public function textTweet()
	{
		$content = $this->content;
		$maxLength = 115;
		$twitterUsername = Lang::get('layout.twitterUsername');
		$maxLengthAddTwitterUsername = $maxLength - strlen($twitterUsername);

		if (strlen($content) > $maxLength)  {
			$content = substr($content, 0, $maxLength);
			$lastSpace = strrpos($content, " ");

			// After the space, add …
			$content = substr($content, 0, $lastSpace).'…';
		}
		elseif (strlen($content) <= $maxLengthAddTwitterUsername) {
			$content .= ' '.$twitterUsername;
		}

		return urlencode($content.' '.URL::route('quotes.show', array($this->id), true));
	}

	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function favorites()
	{
		return $this->hasMany('FavoriteQuote');
	}

	public function favorite()
	{
		return $this->hasOne('FavoriteQuote', 'quote_id', 'id');
	}

	public function getTotalCommentsAttribute()
	{
		// If the quote is not published, obviously we have no comments
		if (!$this->isPublished())
			return 0;

		return Cache::rememberForever(self::$cacheNameNbComments.$this->id, function()
		{
			return $this->hasMany('Comment')->count();
		});
	}

	public function getHasFavoritesAttribute()
	{
		return ($this->total_favorites > 0);
	}

	public function getTotalFavoritesAttribute()
	{
		// If the quote is not published, obviously we have no favorites
		if (!$this->isPublished())
			return 0;

		return Cache::rememberForever(self::$cacheNameNbFavorites.$this->id, function()
		{
			return $this->hasMany('FavoriteQuote')->count();
		});
	}

	public function getHasCommentsAttribute()
	{
		return ($this->total_comments > 0);
	}

	public function isFavoriteForCurrentUser()
	{
		// Try to get information from cache
		if (Auth::check()) {
			// Time for cache
			$expiresAt = Carbon::now()->addMinutes(10);

			$favoriteQuotes = Cache::remember(FavoriteQuote::$cacheNameFavoritesForUser.Auth::id(), $expiresAt, function()
			{
				return FavoriteQuote::currentUser()->select('quote_id')->get()->lists('quote_id');
			});

			return in_array($this->id, $favoriteQuotes);
		}

		return false;
	}

	public function scopeCreatedToday($query)
	{
		return $query->whereBetween('created_at', array(Carbon::today(), Carbon::today()->addDay()));
	}

	public function scopeUpdatedToday($query)
	{
		return $query->whereBetween('updated_at', array(Carbon::today(), Carbon::today()->addDay()));
	}

	public function scopeWaiting($query)
	{
		return $query->where('approved', '=', '0');
	}

	public function scopeRefused($query)
	{
		return $query->where('approved', '=', '-1');
	}

	public function scopePending($query)
	{
		return $query->where('approved', '=', '2');
	}

	public function scopePublished($query)
	{
		return $query->where('approved', '=', '1');
	}

	public function scopeForUser($query, $user)
	{
		return $query->where('user_id', '=', $user->id);
	}

	public function scopeOrderDescending($query)
	{
		return $query->orderBy('created_at', 'DESC');
	}

	public function scopeOrderAscending($query)
	{
		return $query->orderBy('created_at', 'ASC');
	}

	public function scopeRandom($query)
	{
		// Here we use a constant in the MySQL RAND function
		// so that quotes will be always be in the same "order"
		// even if they are not ordered
		// ref: http://dev.mysql.com/doc/refman/5.0/en/mathematical-functions.html#function_rand
		return $query->orderBy(DB::raw('RAND(42)'));
	}

	public function isPublished()
	{
		return ($this->approved == 1);
	}

	public function isPending()
	{
		return ($this->approved == 2);
	}

	public function isWaiting()
	{
		return ($this->approved == 0);
	}

	public function isRefused()
	{
		return ($this->approved == -1);
	}

	/**
	 * Lighten or darken a color from an hexadecimal code
	 * @author http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
	 * @param string $hex The color in hexadecimal
	 * @param int $steps Steps should be between -255 and 255. Negative = darker, positive = lighter
	 * @return string The computed hexadecimal color
	 */
	public static function adjustBrightness($hex, $steps)
	{
		$steps = max(-255, min(255, $steps));

    	// Format the hex color string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		}

    	// Get decimal values
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));

    	// Adjust number of steps and keep it inside 0 to 255
		$r = max(0, min(255,$r + $steps));
		$g = max(0, min(255,$g + $steps));
		$b = max(0, min(255,$b + $steps));

		$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

		return '#'.$r_hex.$g_hex.$b_hex;
	}
}
