<?php namespace TeenQuotes\Users\Observers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Newsletters\Models\Newsletter;

class UserObserver {

	/**
	 * Will be triggered when a model is created
	 * @param User $user
	 */
	public function created($user)
	{
		// Subscribe the user to the weekly newsletter
		$repo = App::make('TeenQuotes\Newsletters\Repositories\NewsletterRepository');
		$repo->createForUserAndType($user, Newsletter::WEEKLY);

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