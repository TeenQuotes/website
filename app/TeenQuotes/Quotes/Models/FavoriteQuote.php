<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Models;

use Eloquent;
use TeenQuotes\Quotes\Models\Relations\FavoriteQuoteTrait as FavoriteQuoteRelationsTrait;
use TeenQuotes\Quotes\Models\Scopes\FavoriteQuoteTrait as FavoriteQuoteScopesTrait;

class FavoriteQuote extends Eloquent
{
    use FavoriteQuoteRelationsTrait, FavoriteQuoteScopesTrait;

    protected $table = 'favorite_quotes';

    protected $fillable = [];
}
