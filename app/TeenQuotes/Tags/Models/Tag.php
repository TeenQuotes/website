<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tags\Models;

use Eloquent;
use TeenQuotes\Tags\Models\Relations\TagTrait;

class Tag extends Eloquent
{
    use TagTrait;

    /**
     * Tell we don't use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable columns.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
