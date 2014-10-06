<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Auth;

class AuthHelper extends Module {
	/**
	 * Fill the sign in form and log in
	 * @param  string $login         
	 * @param  string $passwordClear
	 */
	public function signIn($login, $passwordClear)
	{
		// Will be automatically hashed
		$password = $passwordClear;

		$this->getModule('FunctionalHelper')->haveAnAccount(compact('login', 'password'));
		
		$this->getModule('NavigationHelper')->navigateToTheSignInPage();
		$this->getModule('FormFillerHelper')->fillSigninForm($login, $passwordClear);
	}

	public function checkThatIHaveBeenLoggedIn()
	{
		$I = $this->getModule('Laravel4');
		
		$I->amOnRoute('home');
		$this->getModule('FunctionalHelper')->seeSuccessFlashMessage('Nice to see you :)');
		$I->see('My profile', '.navbar');
		$I->assertTrue(Auth::check());
	}

	public function performLogoutFlow()
	{
		$I = $this->getModule('Laravel4');
		
		$this->getModule('NavigationHelper')->navigateToMyProfile();
		$I->click('Log out');
	}
}