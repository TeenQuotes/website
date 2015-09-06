<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Models\Relations;

use TeenQuotes\Users\Models\User;

trait ProfileVisitorTrait
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id', 'id');
    }
}
