<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Stories\Validation;

use TeenQuotes\Tools\Validation\Validator as BaseValidator;

class StoryValidator extends BaseValidator
{
    /**
     * The validation rules when posting a story.
     *
     * @var array
     */
    protected $rulesPosting = [
        'represent_txt' => 'required|min:100|max:1000',
        'frequence_txt' => 'required|min:100|max:1000',
    ];
}
