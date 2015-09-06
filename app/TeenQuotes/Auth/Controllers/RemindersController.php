<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Auth\Controllers;

use Auth;
use BaseController;
use Input;
use Lang;
use Password;
use Redirect;
use TeenQuotes\Exceptions\TokenNotFoundException;
use URL;
use View;

class RemindersController extends BaseController
{
    public function __construct()
    {
        $this->beforeFilter('guest');
    }

    /**
     * Display the password reminder view.
     *
     * @return \Response
     */
    public function getRemind()
    {
        return View::make('password.remind');
    }

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return \Response
     */
    public function postRemind()
    {
        $response = Password::remind(Input::only('email'), function ($message) {
            $message->subject(Lang::get('auth.passwordReminderEmailSubject'));
        });

        switch ($response) {
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response))->withInput(Input::only('email'));

            case Password::REMINDER_SENT:
                return Redirect::back()->with('success', Lang::get($response));
        }
    }

    /**
     * Display the password reset view for the given token.
     *
     * @return \Response
     */
    public function getReset()
    {
        $token = Input::get('token', null);

        if (is_null($token)) {
            throw new TokenNotFoundException();
        }

        $data = [
            'token'               => $token,
            'contactHumanTitle'   => Lang::get('auth.contactHumanTitle'),
            'contactHumanContent' => Lang::get('auth.contactHumanContent', ['url' => URL::route('contact')]),
            'pageTitle'           => Lang::get('auth.resetPasswordPageTitle'),
            'pageDescription'     => Lang::get('auth.resetPasswordPageDescription'),
        ];

        return View::make('password.reset', $data);
    }

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return \Response
     */
    public function postReset()
    {
        // Here we don't use password_confirmation but we keep it
        // to call the reset function
        $credentials = [
            'email'                 => Input::get('email'),
            'token'                 => Input::get('token'),
            'password'              => Input::get('password'),
            'password_confirmation' => Input::get('password'),
        ];

        $response = Password::reset($credentials, function ($user, $password) {
            // Update the password in database
            $user->password = $password;
            $user->save();

            // Log in the user
            Auth::login($user);
        });

        switch ($response) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return Redirect::back()->with('warning', Lang::get($response))->withInput(Input::only('email', 'token'));

            case Password::PASSWORD_RESET:
                return Redirect::route('home')->with('success', Lang::get('auth.welcomeBackPasswordChanged', ['login' => Auth::user()->login]));
        }
    }
}
