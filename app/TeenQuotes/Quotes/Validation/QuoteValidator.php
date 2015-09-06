<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Validation;

use TeenQuotes\Tools\Validation\Validator as BaseValidator;

class QuoteValidator extends BaseValidator
{
    /**
     * The validation rules when posting a quote.
     *
     * @var array
     */
    protected $rulesPosting = [
        'content'              => 'required|min:50|max:300|unique:quotes,content',
        'quotesSubmittedToday' => 'required|integer|between:0,4',
    ];

    /**
     * Validation rules for the number of quotes submitted today.
     *
     * @var array
     */
    protected $rulesNbSubmittedToday = [
        'quotesSubmittedToday' => 'required|integer|between:0,4',
    ];

    /**
     * Validation rules when moderating a quote.
     *
     * @var array
     */
    protected $rulesModerating = [
        'content' => 'required|min:50|max:300',
    ];
}
