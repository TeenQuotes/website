<?php namespace TeenQuotes\Users\Observers;

use App, Lang, Mail;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Newsletters\Models\Newsletter;

class UserObserver {

	/**
	 * @var TeenQuotes\Newsletters\Repositories\NewsletterRepository
	 */
	private $newsletterRepo;

	public function __construct()
	{
		$this->newsletterRepo = App::make('TeenQuotes\Newsletters\Repositories\NewsletterRepository');
	}

	/**
	 * Will be triggered when a model is created
	 * @param TeenQuotes\Users\Models\User $user
	 */
	public function created($user)
	{
		// Subscribe the user to the weekly newsletter
		$this->newsletterRepo->createForUserAndType($user, Newsletter::WEEKLY);

		$data = [
			'login' => $user->login,
			'email' => $user->email,
		];

		// Send the welcome email via SMTP
		new MailSwitcher('smtp');
		Mail::send('emails.welcome', $data, function($m) use($data)
		{
			$m->to($data['email'], $data['login'])->subject(Lang::get('auth.subjectWelcomeEmail', ['login' => $data['login']]));
		});
	}
}