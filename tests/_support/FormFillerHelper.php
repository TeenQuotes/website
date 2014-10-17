<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TeenQuotes\Users\Models\User;

class FormFillerHelper extends Module {

	public function fillSigninForm($login, $password)
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('Login', $login);
		$I->fillField('Password', $password);
		$I->click('Log me in!', 'form');
	}

	public function fillAddQuoteForm()
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('#content-quote', Str::random(150));
		$I->click('Submit my quote!');
	}

	public function fillAddCommentForm($text)
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('#content-comment', $text);
		$I->click('Add my comment!');
	}

	public function fillSearchForm($search)
	{
		$I = $this->getModule('Laravel4');

		$I->seeCurrentRouteIs('search.form');
		$I->fillField("#search", $search);
		$I->click('Look for this!');
	}

	/**
	 * Fill the edit profile form with the given key-value pairs
	 * @param  array  $params The key-values pairs. Required keys: gender, birthdate (YYYY-MM-DD), country_name, city, about_me. Optional: avatar (filename)
	 */
	public function fillEditProfileFormWith(array $params)
	{
		$I = $this->getModule('Laravel4');

		$I->selectOption('input[name=gender]', $params['gender']);
		$I->fillField('Birthdate', $params['birthdate']);
		$I->selectOption('select[name=country]', $params['country_name']);
		$I->fillField('City', $params['city']);
		$I->fillField('About me', $params['about_me']);

		// If an avatar was given, attach it to the form
		if (array_key_exists('avatar', $params))
			$I->attachFile('input#avatar', $params['avatar']);

		$I->click('Edit my profile!');
	}

	/**
	 * Fill the password reset form for a given user
	 * @param  User   $u The given user
	 */
	public function fillPasswordResetFormFor(User $u)
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('#email', $u->email);
		$I->click('Reset my password!');
	}

	/**
	 * Fill the delete account form
	 * @param  string $password     The clear password
	 * @param  string $confirmation The confirmation word
	 */
	public function fillDeleteAccountForm($password, $confirmation)
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('#delete-account #password', $password);
		$I->fillField('#delete-confirmation', $confirmation);
		$I->click('Delete my account');
	}

	public function fillRegistrationFormFor($login)
	{
		$I = $this->getModule('Laravel4');

		// Set a dummy IP address
		$_SERVER['REMOTE_ADDR'] = '200.22.22.22';

		$I->seeInTitle('Create an account');
		$I->see('Create your account');
		$I->fillField('#login-signup', $login);
		$I->fillField('#email-signup', $login.'@yahoo.com');
		$I->fillField('#password', 'azerty22');
		$I->click("#submit-form");
	}

	/**
	 * Fill the "update my password" form on the user's profile
	 * @param  string $password       The new password
	 * @param  string $passwordRepeat The new repeated password
	 */
	public function fillChangePasswordForm($password, $passwordRepeat)
	{
		$I = $this->getModule('Laravel4');

		$I->fillField('New password', $password);
		$I->fillField('Confirm your password', $passwordRepeat);
		$I->click('Change my password!');
	}

	public function fillUserSettingsForm(array $params)
	{
		$I = $this->getModule('Laravel4');

		$I->selectOption('select[name=colors]', $params['color']);

		foreach (['notification_comment_quote', 'hide_profile', 'daily_newsletter', 'weekly_newsletter'] as $value) {
			if ($params[$value] == 1)
				$I->checkOption('input[name='.$value.']');
			else
				$I->uncheckOption('input[name='.$value.']');
		}

		// Submit the form
		$I->click('Edit my settings!');
		$I->seeCurrentRouteIs('users.edit', Auth::user()->login);
		$I->see('Your settings have been changed');
	}
}