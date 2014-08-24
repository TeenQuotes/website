<?php

class Quote extends Toloquent {
	
	/**
	 * Constants associated with the approved field of the quote
	 */
	const REFUSED   = -1;
	const WAITING   = 0;
	const PUBLISHED = 1;
	const PENDING   = 2;

	protected $fillable = [];

	protected $hidden = ['updated_at'];

	/**
	 * Adding customs attributes to the object
	 * @var array
	 */
	protected $appends = ['has_comments', 'total_comments', 'is_favorite'];

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rulesAdd = [
		'content'              => 'required|min:50|max:300|unique:quotes,content',
		'quotesSubmittedToday' => 'required|integer|between:0,5',
	];

	/**
	 * The colors that will be used for quotes on the admin page
	 * @var array
	 */
	public static $colors = [
		'#27ae60', '#16a085', '#d35400', '#e74c3c', '#8e44ad', '#F9690E', '#2c3e50', '#f1c40f', '#65C6BB', '#E08283'
	];

	/**
	 * The name of the key to store in cache. Describes the number of comments for a given quote.
	 * @var string
	 */
	public static $cacheNameNbComments = 'nb_comments_';

	/**
	 * The name of the key to store in cache. Describes the number of favorites for a given quote.
	 * @var string
	 */
	public static $cacheNameNbFavorites = 'nb_favorites_';

	/**
	 * The name of the key to store in cache. Describes the quotes for a given page.
	 * @var string
	 */
	public static $cacheNameQuotesPage = 'quotes_homepage_';

	/**
	 * The name of the key to store in cache. Describes the quotes for a given page in API with default pagesize.
	 * @var string
	 */
	public static $cacheNameQuotesAPIPage = 'quotes_api_';

	/**
	 * The name of the key to store in cache. Describes the quotes for a given "random" page.
	 * @var string
	 */
	public static $cacheNameRandomPage = 'quotes_random_';

	/**
	 * The name of the key to store in cache. Describes the quotes for a given "random" page in API with default pagesize.
	 * @var string
	 */
	public static $cacheNameRandomAPIPage = 'quotes_random_api';

	/**
	 * The name of the key to store in cache. Describes the number of quotes that have been published.
	 * @var string
	 */
	public static $cacheNameNumberPublished = 'nb_quotes_published';

	/**
	 * Store colors that will be use to display quotes in an associative array: quote_id => css_class_name. This array is stored in session to be used when displaying a single quote.
	 * @param  array $quotesIDs IDs of the quotes
	 * @param  string $colors   If we want to use different colors, give a string here. Example: orange|blue|red..
	 * @return array            The associative array: quote_id => color
	 */
	public static function storeQuotesColors($quotesIDs, $color = null)
	{
		$colors = array();

		// We will build an array if we have at least one quote
		if (count($quotesIDs) >= 1) {		
			$func = function($value) use ($color) {
				if (is_null($color))
					return 'color-'.$value;
				else
					return 'color-'.$color.'-'.$value;
			};

			$colors = array_map($func, range(1, count($quotesIDs)));
			$colors = array_combine($quotesIDs, $colors);
		}

		// Store it in session
		Session::set('colors.quote', $colors);

		return $colors;
	}

	/**
	 * Return the text for the Twitter Card. Displayed on single quote page.
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
	 * The text that will be tweeted. Given to the Twitter sharer.
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
		elseif (strlen($content) <= $maxLengthAddTwitterUsername)
			$content .= ' '.$twitterUsername;

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
			return $this->comments->count();
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
			return $this->favorites->count();
		});
	}

	public function getHasCommentsAttribute()
	{
		return ($this->total_comments > 0);
	}

	public function getIsFavoriteAttribute()
	{
		return $this->isFavoriteForCurrentUser();
	}

	public static function getRandomColors()
	{
		$colors = self::$colors;
		shuffle($colors);

		return $colors;
	}

	public function isFavoriteForCurrentUser()
	{
		// Try to get information from cache
		if (Auth::check() OR !empty(ResourceServer::getOwnerId())) {
			// Time for cache
			$expiresAt = Carbon::now()->addMinutes(10);

			$id = Auth::check() ? Auth::id() : ResourceServer::getOwnerId();

			// Here we use the direct call to cache because we don't
			// want to create a User model just to call the dedicated method
			$favoriteQuotes = Cache::remember(FavoriteQuote::$cacheNameFavoritesForUser.$id, $expiresAt, function() use($id)
			{
				return FavoriteQuote::forUser($id)->select('quote_id')->get()->lists('quote_id');
			});

			return in_array($this->id, $favoriteQuotes);
		}

		return false;
	}

	public function scopeCreatedToday($query)
	{
		return $query->whereBetween('created_at', [Carbon::today(), Carbon::today()->addDay()]);
	}

	public function scopeUpdatedToday($query)
	{
		return $query->whereBetween('updated_at', [Carbon::today(), Carbon::today()->addDay()]);
	}

	public function scopeWaiting($query)
	{
		return $query->where('approved', '=', self::WAITING);
	}

	public function scopeRefused($query)
	{
		return $query->where('approved', '=', self::REFUSED);
	}

	public function scopePending($query)
	{
		return $query->where('approved', '=', self::PENDING);
	}

	public function scopePublished($query)
	{
		return $query->where('approved', '=', self::PUBLISHED);
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

	/**
	 * Function to search for quotes
	 *
	 * @param  string $search
	 * @return Collection Collection of Quote
	 */
	public static function searchQuotes($search)
	{
		return Quote::
		select('id', 'content', 'user_id', 'approved', 'created_at', 'updated_at', DB::raw("MATCH(content) AGAINST(?) AS `rank`"))
		// $search will NOT be bind here
		// it will be bind when calling setBindings
		->whereRaw("MATCH(content) AGAINST(?)", array($search))
		->where('approved', '=', 1)
		->orderBy('rank', 'DESC')
		->with('user')
		// WARNING 1 corresponds to approved = 1
		// We need to bind it again
		->setBindings([$search, $search, self::PUBLISHED])
		->get();
	}

	public function isPublished()
	{
		return ($this->approved == self::PUBLISHED);
	}

	public function isPending()
	{
		return ($this->approved == self::PENDING);
	}

	public function isWaiting()
	{
		return ($this->approved == self::WAITING);
	}

	public function isRefused()
	{
		return ($this->approved == self::REFUSED);
	}

	public static function nbQuotesPublished()
	{
		$totalQuotes = Cache::rememberForever(Quote::$cacheNameNumberPublished, function()
		{
			return Quote::published()->count();
		});

		return $totalQuotes;
	}

	/**
	 * Register a view action in the Easyrec recommendation engine
	 */
	public function registerViewAction()
	{
		// Try to retrieve the ID of the user
		if (Auth::guest())
			$userRecommendation = !empty(ResourceServer::getOwnerId()) ? ResourceServer::getOwnerId() : null;
		else
			$userRecommendation = Auth::id();
    	
		// Register in the recommendation system
    	Easyrec::view($this->id, "Quote ".$this->id, URL::route("quotes.show", $this->id, false), $userRecommendation, null, null, "QUOTE");
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
		if (strlen($hex) == 3)
			$hex = str_repeat(substr($hex, 0, 1), 2).str_repeat(substr($hex, 1, 1), 2).str_repeat(substr($hex, 2, 1), 2);

		// Get decimal values
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));

		// Adjust number of steps and keep it inside 0 to 255
		$r = max(0, min(255, $r + $steps));
		$g = max(0, min(255, $g + $steps));
		$b = max(0, min(255, $b + $steps));

		$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

		return '#'.$r_hex.$g_hex.$b_hex;
	}
}
