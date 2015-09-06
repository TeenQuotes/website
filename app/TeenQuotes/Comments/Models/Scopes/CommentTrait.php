<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Comments\Models\Scopes;

use TeenQuotes\Quotes\Models\Quote;

trait CommentTrait
{
    public function scopeOrderDescending($query)
    {
        return $query->orderBy('created_at', 'DESC');
    }

    public function scopeForQuoteId($query, $id)
    {
        return $query->where('quote_id', '=', $id);
    }

    public function scopeForQuote($query, Quote $q)
    {
        return $query->where('quote_id', '=', $q->id);
    }
}
