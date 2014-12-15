<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => $_ENV['MAILGUN_DOMAIN'],
		'secret' => $_ENV['MAILGUN_SECRET'],
		'pubkey' => $_ENV['MAILGUN_PUBKEY'],
	],

	'mandrill' => [
		'secret' => $_ENV['MANDRILL_SECRET'],
	],

	'stripe' => [
		'model'  => 'TeenQuotes\Users\Models\User',
		'secret' => '',
	],

	'mailchimp' => [
		'secret' => $_ENV['MAILCHIMP_SECRET']
	],

	'easyrec' => [
		'apiKey'   => $_ENV['EASYREC_APIKEY'],
		'tenantID' => 'teenquotes_'.App::environment()
	],
];