<?php namespace TeenQuotes\Newsletters\Models\Scopes;

use InvalidArgumentException;
use User;

trait NewsletterTrait {
	
	public function scopeType($query, $type)
	{
		if (!in_array($type, [self::WEEKLY, self::DAILY]))
			throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type." was given.");

		return $query->whereType($type);
	}

	public function scopeForUser($query, User $user)
	{
		return $query->where('user_id', '=', $user->id);
	}
}