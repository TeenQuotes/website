<?php namespace TeenQuotes\Users\Observers;

use App, Carbon, Lang, Mail;
use TeenQuotes\Newsletters\Models\Newsletter;

class UserObserver {

	/**
	 * @var TeenQuotes\Newsletters\NewslettersManager
	 */
	private $newsletterManager;

	/**
	 * @var TeenQuotes\Mail\UserMailer
	 */
	private $userMailer;

	public function __construct()
	{
		$this->newsletterManager = App::make('TeenQuotes\Newsletters\NewslettersManager');
		$this->userMailer = App::make('TeenQuotes\Mail\UserMailer');
	}

	/**
	 * Will be triggered when a model is created
	 * @param TeenQuotes\Users\Models\User $user
	 */
	public function created($user)
	{
		// Subscribe the user to the weekly newsletter
		$this->newsletterManager->createForUserAndType($user, Newsletter::WEEKLY);

		$this->sendWelcomeEmail($user);
	}

	private function sendWelcomeEmail($user)
	{
		$data = [
			'login' => $user->login,
			'email' => $user->email,
		];

		$subject = Lang::get('auth.subjectWelcomeEmail', ['login' => $data['login']]);
		$this->userMailer->sendLater('emails.welcome',
			$user,
			$data,
			$subject,
			null, // Use the default driver
			Carbon::now()->addMinutes(10) // Defer the email
		);
	}
}