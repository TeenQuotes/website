<?php
namespace TeenQuotes\Mail;

use Illuminate\Support\Facades\Config;

class MailSwitcher {

	function __construct($name) {
		
		if (strtolower($name) != 'smtp')
			throw new \InvalidArgumentException($name." is not a valid argument");
		
		// Switch to SMTP
		Config::set('mail.driver', 'smtp');
		Config::set('mail.from', Config::get('mail.from.smtp'));
	}
}