<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Models;

use Eloquent;
use TeenQuotes\Users\Models\Relations\ProfileVisitorTrait as ProfileVisitorRelationsTrait;

class ProfileVisitor extends Eloquent
{
    use ProfileVisitorRelationsTrait;

    protected $table    = 'profile_visitors';
    protected $fillable = [];
}
