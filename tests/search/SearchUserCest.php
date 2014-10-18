<?php

class SearchUserCest {

	private $nbUsers = 5;
	private $searchFor = 'foobar';

	public function _before(SearchTester $I)
	{
		for ($i = 1; $i <= $this->nbUsers; $i++) {
			$I->insertInDatabase(1, 'User', ['login' => Str::random(2).$this->searchFor.$i]);
		}

		// A matching user with an hidden profile
		$I->insertInDatabase(1, 'User', ['login' => Str::random(2).$this->searchFor, 'hide_profile' => true]);
	}

	public function testSearchSuccess(SearchTester $I)
	{
		$I->navigateToTheSearchPage();
		$I->fillSearchForm($this->searchFor);

		$I->seeCurrentRouteIs('search.results', $this->searchFor);
		$I->see('Users', "#users");
		$I->see($this->nbUsers." results");
		$I->seeNumberOfElements('.user-row', $this->nbUsers);
	}
}