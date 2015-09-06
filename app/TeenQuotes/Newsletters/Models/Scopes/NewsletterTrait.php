<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Newsletters\Models\Scopes;

use InvalidArgumentException;
use TeenQuotes\Users\Models\User;

trait NewsletterTrait
{
    public function scopeType($query, $type)
    {
        if (!in_array($type, [self::WEEKLY, self::DAILY])) {
            throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type.' was given.');
        }

        return $query->whereType($type);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', '=', $user->id);
    }
}
