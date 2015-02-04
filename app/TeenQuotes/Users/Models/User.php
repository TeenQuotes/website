<?php namespace TeenQuotes\Users\Models;

use App, Auth, Config, Eloquent, Hash, Queue, ResourceServer;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserTrait;
use Laracasts\Presenter\PresentableTrait;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Users\Models\Relations\UserTrait as UserRelationsTrait;
use TeenQuotes\Users\Models\Scopes\UserTrait as UserScopesTrait;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use PresentableTrait, RemindableTrait, UserTrait, UserRelationsTrait, UserScopesTrait;

	protected $presenter = 'TeenQuotes\Users\Presenters\UserPresenter';

	/**
	 * The database table used by the model.
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 * @var array
	 */
	protected $hidden = ['password', 'ip', 'hide_profile', 'remember_token', 'updated_at', 'avatar', 'security_level', 'notification_comment_quote'];

	/**
	 * Adding customs attributes to the object
	 * @var array
	 */
	protected $appends = ['profile_hidden', 'url_avatar', 'wants_notification_comment_quote', 'is_admin'];

	/**
	 * Adding attributes to the object. These attributes need extra DB queries
	 * @var array
	 */
	public static $appendsFull = ['total_comments', 'favorite_count', 'added_fav_count', 'published_quotes_count', 'is_subscribed_to_daily', 'is_subscribed_to_weekly'];

	/**
	 * @var TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
	 */
	private $favQuoteRepo;

	/**
	 * @var TeenQuotes\Settings\Repositories\SettingRepository
	 */
	private $settingRepo;

	/**
	 * @var TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	private $newsletterRepo;

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	public function __construct($attributes = [])
	{
		parent::__construct($attributes);

		$this->favQuoteRepo   = App::make('TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository');
		$this->settingRepo    = App::make('TeenQuotes\Settings\Repositories\SettingRepository');
		$this->newsletterRepo = App::make('TeenQuotes\Newsletters\Repositories\NewsletterRepository');
		$this->quoteRepo      = App::make('TeenQuotes\Quotes\Repositories\QuoteRepository');
	}

	public function getProfileHiddenAttribute()
	{
		return $this->isHiddenProfile();
	}

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	/**
	 * Tells if the user wants to hide his profile
	 * @return boolean true if we should hide his profile, false otherwise
	 */
	public function isHiddenProfile()
	{
		return $this->hide_profile == 1;
	}

	/**
	 * Tells if a user is a male
	 * @return boolean
	 */
	public function isMale()
	{
		return $this->gender == 'M';
	}

	/**
	 * Tells if a user is a female
	 * @return boolean
	 */
	public function isFemale()
	{
		return ! $this->isMale();
	}

	public function getWantsNotificationCommentQuoteAttribute()
	{
		return $this->wantsEmailComment();
	}

	public function getIsAdminAttribute()
	{
		return $this->security_level == 1;
	}

	public function getTotalComments()
	{
		return $this->comments()->count();
	}

	public function getFavoriteCount()
	{
		return $this->favoriteQuotes()->count();
	}

	/**
	 * Tells if the user is subscribed to the daily or the weekly newsletter
	 * @var string $type The type of the newsletter : weekly|daily
	 * @return boolean true if subscribed, false otherwise
	 */
	public function isSubscribedToNewsletter($type)
	{
		return $this->newsletterRepo->userIsSubscribedToNewsletterType($this, $type);
	}

	public function getIsSubscribedToDaily()
	{
		return $this->isSubscribedToNewsletter(Newsletter::DAILY);
	}

	public function getIsSubscribedToWeekly()
	{
		return $this->isSubscribedToNewsletter(Newsletter::WEEKLY);
	}

	public function getAddedFavCount()
	{
		$idsQuotesPublished = $this->quoteRepo->listPublishedIdsForUser($this);

		if (empty($idsQuotesPublished))
			return 0;

		return $this->favQuoteRepo->nbFavoritesForQuotes($idsQuotesPublished);
	}

	public function getPublishedQuotesCount()
	{
		return $this->quoteRepo->nbPublishedForUser($this);
	}

	/**
	 * Tells if the user wants to receive an email when a comment is
	 * added on one of its quotes
	 * @return boolean true if we should send an email, false otherwise
	 */
	public function wantsEmailComment()
	{
		return $this->notification_comment_quote == 1;
	}

	/**
	 * Returns the old hash of a password. It was used in Teen Quotes v2
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
	 * Get the array of colors to use for the published quotes of the user
	 * @return string The name of the color to use for the user's instance. Example: blue|red|orange
	 */
	public function getColorsQuotesPublished()
	{
		$color = $this->settingRepo->findForUserAndKey($this, 'colorsQuotesPublished');

		// We couldn't find a value, get back to the default
		if (is_null($color))
			return Config::get('app.users.defaultColorQuotesPublished');

		return $color->value;
	}

	/**
	 * Get the IDs of the quotes favorited by the user
	 * @return array
	 */
	public function quotesFavorited()
	{
		return $this->favQuoteRepo->quotesFavoritesForUser($this);
	}

	public function registerViewUserProfile()
	{
		if ($this->isTestingEnvironment())
			return;

		// Try to retrieve the ID of the user
		if (Auth::guest())
		{
			$idUserApi = ResourceServer::getOwnerId();
			$viewingUserId = ! empty($idUserApi) ? $idUserApi : null;
		}
		else
			$viewingUserId = Auth::id();

		// Register in the recommendation system
		$data = [
			'viewer_user_id' => $viewingUserId,
			'user_id'        => $this->id,
			'user_login'     => $this->login,
		];

		Queue::push('TeenQuotes\Queues\Workers\EasyrecWorker@viewUserProfile', $data);
	}

	public function getURLAvatarAttribute()
	{
		return $this->present()->avatarLink;
	}

	public function hasPublishedQuotes()
	{
		return $this->getPublishedQuotesCount() > 0;
	}

	public function hasFavoriteQuotes()
	{
		return $this->getFavoriteCount() > 0;
	}

	public function hasPostedComments()
	{
		return $this->getTotalComments() > 0;
	}

	private function isTestingEnvironment()
	{
		return in_array(App::environment(), ['testing', 'codeception']);
	}
}