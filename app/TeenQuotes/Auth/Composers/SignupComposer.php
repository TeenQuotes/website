<?php

namespace TeenQuotes\Auth\Composers;

use Config;
use JavaScript;
use Lang;
use URL;

class SignupComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        JavaScript::put([
            'didYouMean'         => Lang::get('auth.didYouMean'),
            'mailAddressInvalid' => Lang::get('auth.mailAddressInvalid'),
            'mailAddressValid'   => Lang::get('auth.mailAddressValid'),
            'mailAddressUpdated' => Lang::get('auth.mailAddressUpdated'),
            'mailgunPubKey'      => Config::get('services.mailgun.pubkey'),
            'urlLoginValidator'  => URL::route('users.loginValidator', [], true),
        ]);
    }
}
