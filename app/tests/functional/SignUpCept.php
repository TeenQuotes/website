<?php
$I = new FunctionalTester($scenario);
$I->am('a guest');
$I->wantTo("create a Teen Quotes' account");

$login = 'foobar';

$I->navigateToTheSignUpPage();
$I->fillRegistrationFormFor($login);
$I->amOnMyNewProfile($login);