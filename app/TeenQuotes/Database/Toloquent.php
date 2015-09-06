<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Toloquent extends Eloquent
{
    /**
     * Get a model small user's info.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  The original query
     * @param string                                $source Where we can access the user's info. Default to 'user'
     *
     * @return \Illuminate\Database\Query\Builder The modified query
     */
    public function scopeWithSmallUser($query, $source = 'user')
    {
        return $query->with([$source => function ($q) {
            $q->addSelect(['id', 'login', 'avatar', 'hide_profile']);
        }]);
    }
}
