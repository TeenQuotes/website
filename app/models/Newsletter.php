<?php

use TeenQuotes\Models\Relations\NewsletterTrait as NewsletterRelationsTrait;
use TeenQuotes\Models\Scopes\NewsletterTrait as NewsletterScopesTrait;

class Newsletter extends Eloquent {
	
	use NewsletterRelationsTrait, NewsletterScopesTrait;
	
	/**
	 * Constants associated with the newletter type
	 */
	const DAILY  = 'daily';
	const WEEKLY = 'weekly';

	protected $fillable = [];

	public function isDaily()
	{
		return ($this->type == self::DAILY);
	}

	public function isWeekly()
	{
		return ($this->type == self::WEEKLY);
	}

	/**
	 * Create a newsletter item for the given user
	 * @var User $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
 	 * @return void
	 */
	public static function createNewsletterForUser(User $user, $type)
	{
		if (!in_array($type, [self::WEEKLY, self::DAILY]))
			throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type." was given.");

		if ($user->isSubscribedToNewsletter($type))
			throw new InvalidArgumentException("The user is already subscribed to the newsletter of type ".$type.".");

		$newsletter          = new Newsletter;
		$newsletter->type    = $type;
		$newsletter->user_id = $user->id;
		$newsletter->save();
	}
}