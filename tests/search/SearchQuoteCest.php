<?php

class SearchQuoteCest {

	private $nbQuotes = 5;
	private $searchFor = 'foobar';

	public function _before(SearchTester $I)
	{
		for ($i = 1; $i <= $this->nbQuotes; $i++) {
			$I->insertInDatabase(1, 'Quote', ['content' => Str::random(50).' '.$this->searchFor.' '.Str::random(20)]);
		}
	}

	public function testSearchTooSmall(SearchTester $I)
	{
		$I->navigateToTheSearchPage();
		$I->fillSearchForm('ab');

		$I->seeCurrentRouteIs('search.form');
		$I->seeFormError('The search must be at least 3 characters.');
	}
}