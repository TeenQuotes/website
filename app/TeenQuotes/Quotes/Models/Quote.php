<?php namespace TeenQuotes\Quotes\Models;

use Carbon;
use Easyrec;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;
use Laracasts\Presenter\PresentableTrait;
use ResourceServer;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Models\Relations\QuoteTrait as QuoteRelationsTrait;
use TeenQuotes\Quotes\Models\Scopes\QuoteTrait as QuoteScopesTrait;
use TeenQuotes\Users\Models\User;
use Toloquent;

class Quote extends Toloquent {
	
	use PresentableTrait, QuoteRelationsTrait, QuoteScopesTrait;

	protected $presenter = 'TeenQuotes\Quotes\Presenters\QuotePresenter';

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
	 * The name of the key to store in cache. Describes the quotes for a given page in API with default pagesize.
	 * @var string
	 */
	public static $cacheNameQuotesAPIPage = 'quotes_api_';

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
	 * @var TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
	 */
	private $favQuoteRepo;

	function __construct($attributes = [])
	{
		parent::__construct($attributes);
		
		$this->favQuoteRepo = App::make('TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository');
	}

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

	public function getTotalCommentsAttribute()
	{
		// If the quote is not published, obviously we have no comments
		if ( ! $this->isPublished())
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
		if ( ! $this->isPublished())
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
		$idUserApi = ResourceServer::getOwnerId();
		
		// Try to get information from cache
		if (Auth::check() OR ! empty($idUserApi)) {
			// Time for cache
			$expiresAt = Carbon::now()->addMinutes(10);

			$id = Auth::check() ? Auth::id() : ResourceServer::getOwnerId();
			$favQuoteRepo = $this->favQuoteRepo;

			// Here we use the direct call to cache because we don't
			// want to create a User model just to call the dedicated method
			$favoriteQuotes = Cache::remember(FavoriteQuote::$cacheNameFavoritesForUser.$id, $expiresAt, function() use($favQuoteRepo, $id)
			{
				return $favQuoteRepo->quotesFavoritesForUser($id);
			});

			return in_array($this->id, $favoriteQuotes);
		}

		return false;
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

	/**
	 * Register a view action in the Easyrec recommendation engine
	 */
	public function registerViewAction()
	{
		if ( ! in_array(App::environment(), ['testing', 'codeception'])) {		
			// Try to retrieve the ID of the user
			if (Auth::guest()) {
				$idUserApi = ResourceServer::getOwnerId();
				$userRecommendation = ! empty($idUserApi) ? $idUserApi : null;
			}
			else
				$userRecommendation = Auth::id();
			
			// Register in the recommendation system
			Easyrec::view($this->id, "Quote ".$this->id, URL::route("quotes.show", $this->id, false), $userRecommendation, null, null, "QUOTE");
		}
	}

	/**
	 * Get the cache array that describes the tag to retrieve quotes for a given user, by approved
	 * @param  TeenQuotes\Users\Models\User   $u
	 * @param  string $approve
	 * @return array
	 */
	public static function getCacheNameForUserAndApproved(User $u, $approve)
	{
		if (! in_array($approve, ['pending', 'refused', 'waiting', 'published']))
			throw new InvalidArgumentException("Wrong approved type. Got ".$approve);

		return ['quotes', 'user', $u->id, $approve];
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
