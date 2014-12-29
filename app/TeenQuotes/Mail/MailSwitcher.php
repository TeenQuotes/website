<?php namespace TeenQuotes\Mail;

use App, Config, InvalidArgumentException, Session;

class MailSwitcher {

	public function __construct($driver)
	{
		// Do not change the configuration on a testing environment
		if ($this->isTestingEnvironment()) return;

		self::guardDriver($driver);

		// Postfix is not always installed on developers' computer
		// We will fallback to SMTP
		if (App::environment() == 'local') $driver = 'smtp';


		if ($this->driverNeedsChange($driver)) {

			// Update the configuration
			switch (strtolower($driver)) {
				case 'smtp':
					// Switch to SMTP
					Config::set('mail.driver', 'smtp');
					Config::set('mail.from', Config::get('mail.from.smtp'));
					break;

				case 'mandrill':
					// Switch to Postfix
					Config::set('mail.driver', 'mandrill');
					Config::set('mail.from', Config::get('mail.from'));
					break;
			}

			// Since we have changed the transport layer,
			// we need to register again the service provider
			App::register('TeenQuotes\Mail\MailServiceProvider');
		}
	}

	public static function getAvailableDrivers()
	{
		return ['smtp', 'mandrill'];
	}

	public static function presentAvailableDrivers()
	{
		return implode('|', self::getAvailableDrivers());
	}

	public static function guardDriver($driver)
	{
		if ( ! in_array($driver, self::getAvailableDrivers()))
			throw new InvalidArgumentException("Unknown driver. Got ".$driver.". Possible values are: ".self::presentAvailableDrivers());
	}

	private function isTestingEnvironment()
	{
		return in_array(App::environment(), ['testing', 'codeception', 'codeceptionMysql']);
	}

	private function driverNeedsChange($newDriver)
	{
		return $newDriver != $this->getCurrentDriver();
	}

	private function getCurrentDriver()
	{
		return Config::get('mail.driver');
	}
}