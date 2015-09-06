<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Models\Scopes;

use Auth;
use NotAllowedException;

trait FavoriteQuoteTrait
{
    /**
     * Get the FavoriteQuote for the current user.
     *
     *
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @throws \NotAllowedException when calling this when the visitor is not logged in
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeCurrentUser($query)
    {
        if (!Auth::check()) {
            throw new NotAllowedException("Can't get favorites quotes for a guest user!");
        }

        return $query->where('user_id', '=', Auth::id());
    }

    /**
     * Get FavoriteQuote for a given user.
     *
     * @param Illuminate\Database\Query\Builder $query
     * @param mixed                             $user  int|User User's ID or User object
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeForUser($query, $user)
    {
        if (is_numeric($user)) {
            $user_id = (int) $user;

            return $query->where('user_id', '=', $user_id);
        }

        return $query->where('user_id', '=', $user->id);
    }
}
