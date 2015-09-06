<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Countries\Models;

use Eloquent;
use Laracasts\Presenter\PresentableTrait;
use LaraSetting;
use TeenQuotes\Countries\Models\Relations\CountryTrait as CountryRelationsTrait;

class Country extends Eloquent
{
    use CountryRelationsTrait, PresentableTrait;

    /**
     * The name of the presenter class.
     *
     * @var string
     */
    protected $presenter = 'TeenQuotes\Countries\Presenters\CountryPresenter';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * Does the model include timestamps?
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable properties.
     *
     * @var array
     */
    protected $fillable = ['name', 'country_code'];

    /**
     * The ID of the United States.
     *
     * @var int
     */
    const ID_UNITED_STATES = 224;

    public static function getDefaultCountry()
    {
        // If we have the information in the config file, return it
        if (LaraSetting::has('countries.defaultCountry')) {
            return LaraSetting::get('countries.defaultCountry');
        }

        // We have no clue, return the ID of the USA
        return self::ID_UNITED_STATES;
    }
}
