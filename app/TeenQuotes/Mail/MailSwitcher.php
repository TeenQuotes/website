<?php namespace TeenQuotes\Mail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class MailSwitcher {

	function __construct($name) {
		
		$possibleValues = ['smtp', 'sendmail'];
		
		if (!in_array(strtolower($name), $possibleValues))
			throw new InvalidArgumentException($name." is not a valid argument. Possible values are: ".implode('|', $possibleValues).'.');

		// Postfix is not always installed on developers' computer
		// We will fallback to SMTP
		if (App::environment() == 'local')
			$name = 'smtp';
		
		switch (strtolower($name)) {
			case 'smtp':
				// Switch to SMTP
				Config::set('mail.driver', 'smtp');
				Config::set('mail.from', Config::get('mail.from.smtp'));
				break;

			case 'sendmail':
				// Switch to Postfix
				Config::set('mail.driver', 'sendmail');
				Config::set('mail.from', Config::get('mail.from.sendmail'));
				break;
		}
	}
}