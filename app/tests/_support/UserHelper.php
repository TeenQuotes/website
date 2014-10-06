<?php namespace Codeception\Module;

use Carbon\Carbon;
use Codeception\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserHelper extends Module {

	public function assertMySettingsHaveDefaultValues()
	{
		$this->assertMySettingsHaveTheseValues([
			'color'                      => $this->getDefaultColorForPublishedQuotes(),
			// Receive a notification if a comment is posted on one of my quotes
			'notification_comment_quote' => 1,
			// Profile not hidden
			'hide_profile'               => 0,
			// Subscribed to the weekly newsletter
			'daily_newsletter'           => 0,
			'weekly_newsletter'          => 1
		]);
	}

	/**
	 * Get the default color for published quotes on a user's profile
	 * @return string
	 */
	private function getDefaultColorForPublishedQuotes()
	{
		return ucfirst(Config::get('app.users.defaultColorQuotesPublished'));
	}

	public function assertMySettingsHaveTheseValues(array $params)
	{
		$I = $this->getModule('Laravel4');
		
		$I->seeOptionIsSelected('select[name=colors]', $params['color']);
		
		foreach (['notification_comment_quote', 'hide_profile', 'daily_newsletter', 'weekly_newsletter'] as $value) {
			if ($params[$value] == 1)
				$I->seeCheckboxIsChecked('input[name='.$value.']');
			else 
				$I->dontSeeCheckboxIsChecked('input[name='.$value.']');
		}
	}

	public function hideProfileForCurrentUser()
	{
		$this->getModule('NavigationHelper')->navigateToMyEditProfilePage();

		$this->getModule('FormFillerHelper')->fillUserSettingsForm([
			'notification_comment_quote' => 1,
			'hide_profile'               => 1,
			'daily_newsletter'           => 0,
			'weekly_newsletter'          => 1,
			'color'                      => $this->getDefaultColorForPublishedQuotes(),
		]);
	}

	public function assertEditProfileFormIsFilledWith(array $params)
	{
		$I = $this->getModule('Laravel4');

		$I->seeOptionIsSelected('input[name=gender]', $params['gender']);
		$I->see($params['birthdate']);
		$I->seeOptionIsSelected('select[name=country]', $params['country_name']);
		$I->see($params['city']);
		$I->see($params['about_me']);
	}

	/**
	 * Assert that the logged in user has got the given key-values pairs for its profile
	 * @param  array  $params The key-values pairs. Required keys: gender, birthdate (YYYY-MM-DD), country_name, city, about_me
	 */
	public function assertProfileHasBeenChangedWithParams(array $params)
	{
		$I = $this->getModule('Laravel4');
		
		$I->seeCurrentRouteIs('users.edit', Auth::user()->login);
		$this->getModule('FunctionalHelper')->seeSuccessFlashMessage('You have a brand new profile');

		$this->getModule('NavigationHelper')->navigateToMyProfile();

		$I->see($params['country_name']);
		$I->see($params['city']);
		$I->see($params['about_me']);
		$age = $this->computeAgeFromYYYMMDD($params['birthdate']);
		$I->see($age.' y/o');

		if ($params['gender'] == 'M')
			$I->see("I'm a man");
		else
			$I->see("I'm a woman");
	}

	/**
	 * Compute the age from a date formatted as YYYY-MM-DD
	 * @param  string $date The date formatted as YYYY-MM-DD
	 * @return int The age
	 */
	private function computeAgeFromYYYMMDD($date)
	{
		// Create an array to have year, month and day
		$parts = explode('-', $date);
		
		return Carbon::createFromDate($parts[0], $parts[1], $parts[2])->age;
	}

	public function assertPasswordHasBeenChanged()
	{
		$I = $this->getModule('Laravel4');
		$u = Auth::user();
		
		$I->seeCurrentRouteIs('users.edit', $u->login);
		$this->getModule('FunctionalHelper')->seeSuccessFlashMessage('Your password has been changed');
	}

	public function amOnMyNewProfile($login)
	{
		$I = $this->getModule('Laravel4');

		$I->amOnRoute('users.show', $login);
		$I->assertTrue(Auth::check());
		$I->seeRecord('users', compact('login'));
		$I->seeElement('#welcome-profile');
	}
}