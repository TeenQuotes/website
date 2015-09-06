<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Mail\Facades;

use Illuminate\Support\Facades\Facade;

class MandrillClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mandrill';
    }
}
