<?php namespace TeenQuotes\Auth\Composers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use JavaScript;

class SignupComposer {

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