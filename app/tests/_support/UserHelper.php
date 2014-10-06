<?php namespace Codeception\Module;

use Carbon\Carbon;
use Codeception\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use User;

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
	 * @param  array  $params The key-values pairs. Required keys: gender, birthdate (YYYY-MM-DD), country_name, city, about_me. Optional: avatar (filename)
	 */
	public function assertProfileHasBeenChangedWithParams(array $params)
	{
		$I = $this->getModule('Laravel4');
		$user = Auth::user();
		
		$I->seeCurrentRouteIs('users.edit', $user->login);
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

		// Check that the URL for the avatar has been set
		// Check that the file has been moved to the expected directory
		if (array_key_exists('avatar', $params)) {
			$avatar = $this->getAvatarNameForUser($user, $params['avatar']);
			$I->assertTrue(Str::endsWith($avatar, $user->avatar));
			$I->getModule('Filesystem')->seeFileFound($avatar, $this->getAvatarsPath());
		}
	}

	/**
	 * Construct the name of an avatar for a user and a filename
	 * @param  User   $u    The user
	 * @param  string $file The filename
	 * @return string
	 */
	private function getAvatarNameForUser(User $u, $file)
	{
		return $u->id.'.'.$this->getExtension($file);
	}

	/**
	 * Extract the extension of a filename in a dumb way
	 * @param  string $filename The filename
	 * @return string The extension
	 */
	private function getExtension($filename)
	{
		$chunks = explode('.', $filename);

		return $chunks[1];
	}

	/**
	 * Get the path where avatars are stored
	 * @return string
	 */
	private function getAvatarsPath()
	{
		return Config::get('app.users.avatarPath').'/';
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