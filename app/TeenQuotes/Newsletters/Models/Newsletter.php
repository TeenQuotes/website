<?php namespace TeenQuotes\Newsletters\Models;

use Eloquent;
use InvalidArgumentException;
use TeenQuotes\Newsletters\Models\Relations\NewsletterTrait as NewsletterRelationsTrait;
use TeenQuotes\Newsletters\Models\Scopes\NewsletterTrait as NewsletterScopesTrait;

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
	 * Get available types of newsletters
	 * @return array
	 */
	public static function getPossibleTypes()
	{
		return [self::WEEKLY, self::DAILY];
	}
}