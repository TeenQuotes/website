<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Comments\Validation;

use TeenQuotes\Tools\Validation\Validator as BaseValidator;

class CommentValidator extends BaseValidator
{
    /**
     * The validation rules when adding a comment.
     *
     * @var array
     */
    protected $rulesPosting = [
        'content'  => 'required|min:10|max:500',
        'quote_id' => 'required|exists:quotes,id',
    ];

    /**
     * The validation rules when editing a comment.
     *
     * @var array
     */
    protected $rulesEditing = [
        'content'  => 'required|min:10|max:500',
    ];
}
