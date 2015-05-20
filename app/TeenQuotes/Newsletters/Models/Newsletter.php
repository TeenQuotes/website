<?php

namespace TeenQuotes\Newsletters\Models;

use Eloquent;
use TeenQuotes\Newsletters\Models\Relations\NewsletterTrait as NewsletterRelationsTrait;
use TeenQuotes\Newsletters\Models\Scopes\NewsletterTrait as NewsletterScopesTrait;

class Newsletter extends Eloquent
{
    use NewsletterRelationsTrait, NewsletterScopesTrait;

    /**
     * Constants associated with the newletter type.
     */
    const DAILY  = 'daily';
    const WEEKLY = 'weekly';

    protected $fillable = [];

    /**
     * Tell if it's the daily newsletter.
     *
     * @return bool
     */
    public function isDaily()
    {
        return ($this->type == self::DAILY);
    }

    /**
     * Tell if it's the weekly newsletter.
     *
     * @return bool
     */
    public function isWeekly()
    {
        return ($this->type == self::WEEKLY);
    }

    /**
     * Get available types of newsletters.
     *
     * @return array
     */
    public static function getPossibleTypes()
    {
        return [self::WEEKLY, self::DAILY];
    }
}
