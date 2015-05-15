<?php

class PasswordForgottenCest
{
    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();
    }

    public function resetAForgottenPassword(FunctionalTester $I)
    {
        $I->am('a Teen Quotes member');
        $I->wantTo('reset my password');

        $I->navigateToTheResetPasswordPage();

        $u = $I->haveAnAccount();

        $I->fillPasswordResetFormFor($u);
        $I->seeSuccessFlashMessage('Password reminder sent!');

        // Assert that the email has been sent
        $I->seeInLastEmailSubjectTo($u->email, 'Password reminder');
        $I->seeInLastEmailTo($u->email, "You've just asked to reset your password");
    }

    public function resetAForgottenPasswordWithAnInvalidEmailAddress(FunctionalTester $I)
    {
        $I->am('not a Teen Quotes member');
        $I->wantTo('reset my password with an unknown email address');

        $I->navigateToTheResetPasswordPage();

        $I->fillPasswordResetFormFor($I->buildUser());
        $I->seeFormError("We can't find a user with that e-mail address");
    }
}
