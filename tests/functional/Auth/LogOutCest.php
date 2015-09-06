<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class LogOutCest
{
    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();
    }

    public function clickOnLogoutOnProfile(FunctionalTester $I)
    {
        $I->am('a Teen Quotes member');
        $I->wantTo('log out from my account');

        $I->logANewUser();

        $I->performLogoutFlow();

        $I->seeSuccessFlashMessage('You have been logged out.');
        $I->assertFalse(Auth::check());
    }
}
