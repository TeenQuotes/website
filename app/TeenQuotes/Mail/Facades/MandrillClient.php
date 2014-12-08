<?php namespace TeenQuotes\Mail\Facades;

use Illuminate\Support\Facades\Facade;

class MandrillClient extends Facade {

	protected static function getFacadeAccessor()
	{
		return 'mandrill';
	}
}