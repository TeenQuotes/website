<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SignInCest
{
    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();
    }

    public function performSigninFlow(FunctionalTester $I)
    {
        $I->am('a Teen Quotes member');
        $I->wantTo('sign in to my Teen Quotes account');

        $I->signIn('foobar42', 'azerty22');
        $I->checkThatIHaveBeenLoggedIn();
    }

    public function wrongLogin(FunctionalTester $I)
    {
        $I->am('a guest');
        $I->wantTo('sign in with an unexisting login.');

        $I->navigateToTheSignInPage();
        $I->fillSigninForm('foobar', 'azerty');
        $I->seeFormError('The selected login was not found.');
    }

    public function wrongPassword(FunctionalTester $I)
    {
        $I->am('a member of Teen Quotes');
        $I->wantTo('sign in with a wrong password.');

        $I->haveAnAccount(['login' => 'foobar', 'password' => 'blahblah']);

        $I->navigateToTheSignInPage();
        $I->fillSigninForm('foobar', 'azerty');
        $I->seeFormError('Your password is invalid.');
    }

    public function tooSmallPassword(FunctionalTester $I)
    {
        $I->am('a member of Teen Quotes');
        $I->wantTo('sign in with a too small password.');

        $I->haveAnAccount(['login' => 'foobar', 'password' => 'blahblah']);

        $I->navigateToTheSignInPage();
        $I->fillSigninForm('foobar', 'ab');
        $I->seeFormError('The password must be at least 6 characters.');
    }
}
