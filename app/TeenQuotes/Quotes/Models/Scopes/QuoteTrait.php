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

use Carbon;
use Config;
use DB;
use TeenQuotes\Users\Models\User;

trait QuoteTrait
{
    /**
     * Get quotes created today.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedToday($query)
    {
        return $query->whereBetween('created_at', [Carbon::today(), Carbon::today()->addDay()]);
    }

    /**
     * Get quotes updated today.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdatedToday($query)
    {
        return $query->whereBetween('updated_at', [Carbon::today(), Carbon::today()->addDay()]);
    }

    /**
     * Get quotes waiting to be published.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWaiting($query)
    {
        return $query->where('approved', '=', self::WAITING);
    }

    /**
     * Get quotes that have been refused.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRefused($query)
    {
        return $query->where('approved', '=', self::REFUSED);
    }

    /**
     * Get quotes that are pending moderation.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('approved', '=', self::PENDING);
    }

    /**
     * Get published quotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('approved', '=', self::PUBLISHED);
    }

    /**
     * Get quotes added by a given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \TeenQuotes\Users\Models\User         $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', '=', $user->id);
    }

    /**
     * Order quotes by descending order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderDescending($query)
    {
        return $query->orderBy('created_at', 'DESC');
    }

    /**
     * Order quotes by ascending order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderAscending($query)
    {
        return $query->orderBy('created_at', 'ASC');
    }

    /**
     * Get random quotes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRandom($query)
    {
        if (Config::get('database.default') != 'mysql') {
            return $query;
        }

        // Here we use a constant in the MySQL RAND function
        // so that quotes will be always be in the same "order"
        // even if they are not ordered
        // ref: http://dev.mysql.com/doc/refman/5.0/en/mathematical-functions.html#function_rand
        return $query->orderBy(DB::raw('RAND(42)'));
    }

    /**
     * Get quotes that have been created after a given date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Carbon\Carbon                        $date
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedAfter($query, Carbon $date)
    {
        return $query->where('created_at', '>=', $date);
    }
}
