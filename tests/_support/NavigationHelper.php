<?php

namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Auth;

class NavigationHelper extends Module
{
    public function navigateToMyProfile()
    {
        $I = $this->getModule('Laravel4');

        $I->assertTrue(Auth::check());
        $I->amOnRoute('home');
        $I->click('My profile', '.nav');
    }

    public function navigateToTheAdminPanel()
    {
        $I = $this->getModule('Laravel4');

        $I->amOnRoute('admin.quotes.index');
    }

    public function navigateToTheSearchPage()
    {
        $I = $this->getModule('Laravel4');

        $I->amOnRoute('home');
        $I->click('Search', '.nav');
    }

    public function navigateToTheStoryPage()
    {
        $I = $this->getModule('Laravel4');

        $I->amOnRoute('home');
        $I->click('Stories', 'footer');
    }

    public function navigateToMyEditProfilePage()
    {
        $I = $this->getModule('Laravel4');
        $u = Auth::user();

        $this->navigateToMyProfile();

        $I->click('Edit my profile');

        // Assert that we can do several actions
        $I->seeCurrentRouteIs('users.edit', $u->login);
        $I->seeInTitle('Edit your profile');
        $I->see('Edit my profile');
        $I->see('Change my password');
        $I->see('Edit my settings');
        $I->see('Delete my account');
    }

    public function navigateToTheResetPasswordPage()
    {
        $I = $this->getModule('Laravel4');

        $this->navigateToTheSignInPage();
        $I->click("I don't remember my password!");
        $I->seeCurrentRouteIs('password.remind');
    }

    public function navigateToTheSignInPage()
    {
        $I = $this->getModule('Laravel4');

        $I->amOnRoute('home');
        $I->click('Log in');
        $I->seeCurrentRouteIs('signin');
    }

    public function navigateToTheSignUpPage()
    {
        $I = $this->getModule('Laravel4');

        $I->amOnRoute('home');
        $I->click('Log in');
        $I->seeCurrentRouteIs('signin');
        $I->click('I want an account!');
        $I->seeCurrentRouteIs('signup');
    }

    public function navigateToTheAddQuotePage()
    {
        $I = $this->getModule('Laravel4');

        $I->amOnRoute('home');
        $I->click('Add your quote');
        $I->seeCurrentRouteIs('addquote');
    }
}
