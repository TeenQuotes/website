<?php 
var_dump(Config::get('database.default'));

$I = new FunctionalTester($scenario);
$I->am('a Teen Quotes member');
$I->wantTo('sign in to my Teen Quotes account');

$I->signIn();

$I->amOnRoute('home');
$I->see('My profile');

$I->assertTrue(Auth::check());