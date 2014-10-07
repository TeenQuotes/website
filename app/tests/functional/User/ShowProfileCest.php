<?php

class ShowProfileCest {

	/**
	 * The authenticated user
	 * @var User
	 */
	private $user;

	/**
	 * Params used to create the user
	 * @var array
	 */
	private $userParams;

	public function _before(FunctionalTester $I)
	{
		$I->createSomePublishedQuotes();
		$this->userParams = [
			'about_me'     => 'Lorem',
			'birthdate'    => '2000-01-12',
			'city'         => 'Paris',
			// ID of Argentina
			'country'      => 10,
			'country_name' => 'Argentina',
			'gender'       => 'F',
		];

		// Do not pass the country name to TestDummy
		// We just want the country name to assert that 
		// the form is filled with the right values
		$overrides = $this->userParams;
		array_forget($overrides, 'country_name');

		$this->user = $I->logANewUser($overrides);
	}

	public function testProfileContainsFullInformation(FunctionalTester $I)
	{
		$I->am('a logged in Teen Quotes member');
		$I->wantTo('verify that my profile contains all my statistics');

		// Expectations
		$data                           = $this->userParams;
		$data['quotes-published-count'] = 2;
		$data['fav-count']              = 5;
		$data['comments-count']         = 4;
		$data['added-fav-count']        = 3;

		// Set the database
		$quotes = $I->insertInDatabase($data['quotes-published-count'], 'Quote', ['user_id' => $this->user->id]);
		$I->insertInDatabase($data['fav-count'], 'FavoriteQuote', ['user_id' => $this->user->id]);
		$I->insertInDatabase($data['comments-count'], 'Comment', ['user_id' => $this->user->id]);
		$I->insertInDatabase($data['added-fav-count'], 'FavoriteQuote', ['quote_id' => $quotes[0]->id]);

		// Assert that these statistics are displayed
		$I->navigateToMyProfile();
		$I->assertProfileContainsInformation($data);
	}
}