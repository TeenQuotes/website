<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Countries\Models\Relations;

use TeenQuotes\Users\Models\User;

trait CountryTrait
{
    public function users()
    {
        return $this->hasMany(User::class, 'country', 'id');
    }
}
