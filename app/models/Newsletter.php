<?php

class Newsletter extends Eloquent {
	protected $fillable = [];

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function scopeType($query, $type)
	{
		if (!in_array($type, ['weekly', 'daily']))
			throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type." was given.");

		return $query->whereType($type);
	}

	public function scopeForUser($query, User $user)
	{
		return $query->where('user_id', '=', $user->id);
	}

	public function isDaily()
	{
		return ($this->type == 'daily');
	}

	public function isWeekly()
	{
		return ($this->type == 'weekly');
	}

	/**
	 * @brief Create a newsletter item for the given user
	 * @var object $user The user instance
	 * @var string $type The type of the newsletter : weekly|daily
 	 * @return void
	 */
	public static function createNewsletterForUser(User $user, $type)
	{
		if (!in_array($type, ['weekly', 'daily']))
			throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type." was given.");

		if ($user->isSubscribedToNewsletter($type))
			throw new InvalidArgumentException("The user is already subscribed to the newsletter of type ".$type.".");

		$newsletter                   = new Newsletter;
		$newsletter->type             = $type;
		$newsletter->user_id          = $user->id;
		$newsletter->save();
	}
}