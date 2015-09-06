<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class DeleteAccountCest
{
    /**
     * The user's login.
     *
     * @var string
     */
    private $userLogin = 'foobar';

    /**
     * The user's password.
     *
     * @var string
     */
    private $userPassword = 'azerty22';

    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();

        $I->signIn($this->userLogin, $this->userPassword);
    }

    public function deleteMyAccount(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('delete my account');

        $I->navigateToMyEditProfilePage();

        $I->fillDeleteAccountForm($this->userPassword, 'DELETE');

        $I->dontSeeRecord('users', ['login' => $this->userLogin]);
        $I->seeSuccessFlashMessage('Your account has been deleted successfully');
    }

    public function deleteMyAccountWithWrongConfirmation(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('delete my account with a wrong confirmation');

        $I->navigateToMyEditProfilePage();

        $I->fillDeleteAccountForm($this->userPassword, 'foo');

        $I->seeFormError('You need to write "DELETE" here');
    }
}
