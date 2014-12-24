<?php namespace TeenQuotes\Mail;

use App;
use Illuminate\Config\Repository as Config;
use TeenQuotes\Users\Models\User;

class UserMailer {

	/**
	 * @var Illuminate\Config\Repository
	 */
	private $config;

	/**
	 * @var Illuminate\Mail\Mailer
	 */
	private $mail;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Send a mail to a user
	 * @param string $viewName The name of the view
	 * @param TeenQuotes\Users\Models\User $user
	 * @param array $data Data to pass the email view
	 * @param string $subject The subject of the email
	 * @param string $driver The name of the driver that we will use to send the email
	 */
	public function send($viewName, User $user, $data, $subject, $driver = null)
	{
		$this->switchDriverIfNeeded($driver);

		// Send the email
		$this->mail->send($viewName, $data, function ($m) use($user, $subject)
		{
			$m->to($user->email, $user->login)->subject($subject);
		});
	}

	/**
	 * Send a delayed mail to a user
	 * @param string $viewName The name of the view
	 * @param TeenQuotes\Users\Models\User $user
	 * @param array $data Data to pass the email view
	 * @param string $subject The subject of the email
	 * @param string $driver The name of the driver that we will use to send the email
	 * @param DateTime|int $delay
	 */
	public function sendLater($viewName, User $user, $data, $subject, $driver = null, $delay = 0)
	{
		$this->switchDriverIfNeeded($driver);

		// Queue the email
		$this->mail->later($delay, $viewName, $data, function ($m) use($user, $subject)
		{
			$m->to($user->email, $user->login)->subject($subject);
		});
	}

	private function switchDriverIfNeeded($driver)
	{
		if (is_null($driver)) $driver = $this->getCurrentDriver();

		$this->guardDriver($driver);

		// Ask to use this driver
		new MailSwitcher($driver);

		$this->registerMailDriver();
	}

	private function getCurrentDriver()
	{
		return $this->config->get('mail.driver');
	}

	private function registerMailDriver()
	{
		$this->mail = App::make('mailer');
	}

	private function guardDriver($driver)
	{
		MailSwitcher::guardDriver($driver);
	}

	private function getAvailableDrivers()
	{
		return MailSwitcher::getAvailableDrivers();
	}

	private function presentAvailableDrivers()
	{
		return MailSwitcher::presentAvailableDrivers();
	}
}