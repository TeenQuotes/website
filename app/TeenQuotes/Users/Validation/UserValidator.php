<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Validation;

use TeenQuotes\Tools\Validation\Validator as BaseValidator;

class UserValidator extends BaseValidator
{
    /**
     * The validation rules when signing in.
     *
     * @var array
     */
    protected $rulesSignin = [
        'password' => 'required|min:6',
        'login'    => 'required|alpha_dash|exists:users,login|min:3|max:20',
    ];

    protected $rulesLogin = [
        'login'    => 'required|alpha_dash|unique:users,login|min:3|max:20',
    ];

    /**
     * The validation rules when signing up.
     *
     * @var array
     */
    protected $rulesSignup = [
        'password' => 'required|min:6',
        'login'    => 'required|alpha_dash|unique:users,login|min:3|max:20',
        'email'    => 'required|email|unique:users,email',
    ];

    /**
     * The validation rules when updating a profile.
     *
     * @var array
     */
    protected $rulesUpdateProfile = [
        'gender'    => 'in:M,F',
        'birthdate' => 'date_format:"Y-m-d"',
        'country'   => 'exists:countries,id',
        'city'      => '',
        'avatar'    => 'image|max:1500',
        'about_me'  => 'max:500',
    ];

    /**
     * The validation rules when deleting an account.
     *
     * @var array
     */
    protected $rulesDestroy = [
        'password'            => 'required|min:6',
        'delete-confirmation' => 'in:DELETE',
    ];

    /**
     * The validation rules when updating a password.
     *
     * @var array
     */
    protected $rulesUpdatePassword = [
        'password' => 'required|min:6|confirmed',
    ];
}
