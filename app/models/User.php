<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'ip');

	/**
	 * The validation rules when updating a profile
	 * @var array
	 */
	public static $rulesUpdate = [
		'gender'    => 'in:M,F',
		'birthdate' => 'date_format:"Y-m-d"',
		'country'   => 'exists:countries,id',
		'city'      => '',
		'avatar'    => 'image|max:500',
		'about_me'  => 'max:500',
	];

	/**
	 * The validation rules when updating a password
	 * @var array
	 */
	public static $rulesUpdatePassword = [
		'password' => 'required|min:6|confirmed',
	];

	/**
	 * The validation rules when signing up
	 * @var array
	 */
	public static $rulesSignup = [
		'login' => 'required|alpha_dash|unique:users,login|min:3|max:20',
		'password' => 'required|min:6',
		'email' => 'required|email|unique:users,email',
	];

	/**
	 * The validation rules when signing in
	 * @var array
	 */
	public static $rulesSignin = [
		'login' => 'required|alpha_dash|exists:users,login|min:3|max:20',
		'password' => 'required|min:6',
	];

	/**
	 * The name of the key to store in cache. Describes quotes published by a user
	 * @var array
	 */
	public static $cacheNameForPublished = 'quotes_published_';

	/**
	 * The name of the key to store in cache. Describes quotes favorited by a user
	 * @var array
	 */

	public static $cacheNameForFavorited = 'quotes_favorited_';

	/**
	 * The name of the key to store in cache. Describes the number of quotes published by a user
	 * @var array
	 */
	public static $cacheNameForNumberQuotesPublished = 'number_quotes_published_';

	/**
	 * The name of the key to store in cache. Describes the colors used for the published quotes of the user
	 * @var array
	 */
	public static $cacheNameForColorsQuotesPublished = 'colors_quotes_published_';

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function countryObject()
	{
		return $this->belongsTo('Country', 'country', 'id');
	}

	public function newsletters()
	{
		return $this->hasMany('Newsletter');
	}

	public function quotes()
	{
		return $this->hasMany('Quote');
	}

	public function settings()
	{
		return $this->hasMany('Setting');
	}

	public function stories()
	{
		return $this->hasMany('Story');
	}

	public function usersVisitors()
	{
		return $this->hasMany('ProfileVisitor', 'user_id', 'id');
	}

	public function usersVisited()
	{
		return $this->hasMany('ProfileVisitor', 'visitor_id', 'id');
	}

	public function favoriteQuotes()
    {
        return $this->belongsToMany('Quote', 'favorite_quotes')->with('user')->orderBy('favorite_quotes.id', 'DESC');
    }

    /**
     * @brief Tells if the user wants to hide his profile
     * @return boolean true if we should hide his profile, false otherwise
     */
    public function isHiddenProfile()
    {
    	return ($this->hide_profile == 1);
    }

    /**
     * @brief Tells if the user wants to receive an email when a comment is
     * added on one of its quotes
     * @return boolean true if we should send an email, false otherwise
     */
    public function wantsEmailComment()
    {
    	return ($this->notification_comment_quote == 1);
    }

    /**
     * @brief Returns the old hash of a password. It was used in Teen Quotes v2
     * @var array $data The data. We need a login and a password
     * @return string The corresponding hash that was used in Teen Quotes v2
     */
    public static function oldHashMethod($data)
    {
    	// This is legacy code. This hash method was used in 2005 by Mangos...
    	// I feel a bit old and stupid right now.
    	return sha1(strtoupper($data['login']).':'.strtoupper($data['password']));
    }


    /**
     * @brief Get the array of colors to use for the published quotes of the user
     * @return array The array of the hexadecimal colors to use for the user's instance
     */
    public function getColorsQuotesPublished()
    {
		// If we have something in cache, return it immediately
		if (Cache::has(self::$cacheNameForColorsQuotesPublished.$this->id))
			return Cache::get(self::$cacheNameForColorsQuotesPublished.$this->id);
		else {

			$colorsAvailable = Config::get('app.users.colorsQuotesPublished');
			$confColor = Setting::
				where('user_id', '=', $this->id)
				->where('key', '=', 'colorsQuotesPublished')
				->first();

			// Set colors to put in cache for the user
			if (is_null($confColor))
				$toPut = $colorsAvailable['blue'];
			else {
				$toPut = $colorsAvailable[$confColor->value];
			}

			// Store in cache
			Cache::forever(self::$cacheNameForColorsQuotesPublished.$this->id, $toPut);

			return $toPut;
		}
    }

    /**
     * @brief Get the URL of the user's avatar
     * @return string The URL to the avatar
     */
    public function getURLAvatar()
    {
    	// Full URL
    	if (strrpos($this->avatar, 'http') !== false)
    		return $this->avatar;
    	// Local URL
    	else {
    		return str_replace('public/', '', Request::root().'/'.Config::get('app.users.avatarPath').'/'.$this->avatar);
    	}
    }

    public function scopeBirthdayToday($query)
    {
    	return $query->where(DB::raw("DATE_FORMAT(birthdate,'%m-%d')"), '=', DB::raw("DATE_FORMAT(NOW(),'%m-%d')"));
    }

    public function scopeNotHidden($query)
    {
    	return $query->where('hide_profile', '=', 0);
    }

    public function scopeHidden($query)
    {
    	return $query->where('hide_profile', '=', 1);
    }

    public function scopePartialLogin($query, $login)
    {
    	return $query->whereRaw('login LIKE ?', ["%$login%"])->orderBy('login', 'ASC');
    }

    public function hasPublishedQuotes()
    {
		// Time to store quotes in cache
		$expiresAt = Carbon::now()->addMinutes(10);
		$user = $this;

		$numberQuotesPublishedForUser = Cache::remember(self::$cacheNameForNumberQuotesPublished.$this->id, $expiresAt, function() use ($user)
			{
				return Quote::forUser($user)
					->published()
					->count();
			});

		return $numberQuotesPublishedForUser > 0;
    }

    public function hasFavoriteQuotes()
    {
    	return FavoriteQuote::forUser($this)->count() > 0;
    }

    /**
     * Tells if the user is subscribed to the daily or the weekly newsletter
     * @var string $type The type of the newsletter : weekly|daily
     * @return boolean true if subscribed, false otherwise
     */
    public function isSubscribedToNewsletter($type)
    {
    	return (Newsletter::forUser($this)->type($type)->count() > 0);
    }

    /**
     * @brief Get the name of the icon to display based on the gender of the user
     * @return string The name of the icon to display : fa-male | fa-female
     */
    public function getIconGender()
    {
    	return ($this->gender == 'M') ? 'fa-male' : 'fa-female';
    }

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
}