<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Settings\Models;

use Eloquent;
use TeenQuotes\Settings\Models\Relations\SettingTrait as SettingRelationsTrait;

class Setting extends Eloquent
{
    use SettingRelationsTrait;

    protected $fillable = ['user_id', 'key', 'value'];
    public $timestamps  = false;
}
