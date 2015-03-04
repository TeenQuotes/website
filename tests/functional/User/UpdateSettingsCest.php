<?php

class UpdateSettingsCest {

	/**
	 * The authenticated user
	 * @var \TeenQuotes\Users\Models\User
	 */
	private $user;

	public function _before(FunctionalTester $I)
	{
		$this->user = $I->logANewUser();
		// Create some published quotes for the logged in user
		$I->createSomePublishedQuotes(['user_id' => $this->user->id]);
	}

	public function updateMySettings(FunctionalTester $I)
	{
		$I->am('a logged in Teen Quotes member');
		$I->wantTo('update my settings');

		$I->navigateToMyEditProfilePage();
		$I->assertMySettingsHaveDefaultValues();

		$newColor = 'red';
		$params = [
			'color'                      => ucfirst($newColor),
			'notification_comment_quote' => 0,
			'hide_profile'               => 1,
			'daily_newsletter'           => 1,
			'weekly_newsletter'          => 0
		];
		$I->fillUserSettingsForm($params);
		$I->assertMySettingsHaveTheseValues($params);

		$I->navigateToMyProfile();
		// Verify that the profile is hidden
		$I->see('Your profile is currently hidden. Only you can see this!');
		// Verify the color of published quotes
		$I->seeElement('.color-'.$newColor.'-1');
	}
}