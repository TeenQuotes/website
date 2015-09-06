<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tags\Models\Relations;

use TeenQuotes\Quotes\Models\Quote;

trait TagTrait
{
    public function quotes()
    {
        return $this->belongsToMany(Quote::class, 'quote_tag', 'tag_id', 'quote_id');
    }
}
