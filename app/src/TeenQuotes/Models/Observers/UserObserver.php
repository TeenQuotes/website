<?php namespace TeenQuotes\Models\Observers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Newsletter;
use TeenQuotes\Mail\MailSwitcher;

class UserObserver {
	
	/**
	 * Will be triggered when a model will be saved
	 * @param  User $model
	 */
	public function created($user)
	{
		// Subscribe the user to the weekly newsletter
		Newsletter::createNewsletterForUser($user, 'weekly');

		$data = [
			'login' => $user->login,
			'email' => $user->email,
		];

		// Send the welcome email via Postfix
		new MailSwitcher('sendmail');
		Mail::send('emails.welcome', $data, function($m) use($data)
		{
			$m->to($data['email'], $data['login'])->subject(Lang::get('auth.subjectWelcomeEmail', ['login' => $data['login']]));
		});
	}
}