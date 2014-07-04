<?php
namespace TeenQuotes\Mail;

use Illuminate\Support\Facades\Config;

class MailSwitcher {

	function __construct($name) {
		
		if (!in_array(strtolower($name), ['smtp', 'sendmail']))
			throw new \InvalidArgumentException($name." is not a valid argument");
		
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