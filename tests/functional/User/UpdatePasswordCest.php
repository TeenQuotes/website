<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class UpdatePasswordCest
{
    /**
     * The authenticated user.
     *
     * @var \TeenQuotes\Users\Models\User
     */
    private $user;

    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();
        $this->user = $I->logANewUser();
    }

    public function updateMyPassword(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('update my password');

        $newPassword = 'azerty42';

        // Update the user's password
        $I->navigateToMyEditProfilePage();
        $I->fillChangePasswordForm($newPassword, $newPassword);
        $I->assertPasswordHasBeenChanged();

        // Log him out
        $I->performLogoutFlow();

        // Sign in again with the new password
        $I->navigateToTheSignInPage();
        $I->fillSigninForm($this->user->login, $newPassword);

        // Assert that we have been authenticated
        $I->assertTrue(Auth::check());
    }

    public function updateMyPasswordWithADifferentConfirmationPassword(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('update my password with a different confirmation password');

        $newPassword     = 'azerty42';
        $confirmPassword = 'azerty22';

        // Try to update the user's password
        $I->navigateToMyEditProfilePage();
        $I->fillChangePasswordForm($newPassword, $confirmPassword);

        // Assert that we can't change the password
        $I->seeFormError('The password confirmation does not match.');
    }
}
