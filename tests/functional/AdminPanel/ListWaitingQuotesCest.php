<?php

class ListWaitingQuotesCest {

	/**
	 * The admin user
	 * @var \TeenQuotes\Users\Models\User
	 */
	private $admin;

	/**
	 * @var \FunctionalTester
	 */
	private $tester;

	/**
	 * The number of quotes created, by approved type
	 * @var array
	 */
	private $nbQuotes;

	public function _before(FunctionalTester $I)
	{
		$this->tester = $I;

		$this->nbQuotes = [
			'pending' => 7,
			'waiting' => 26,
		];

		$I->createSomePublishedQuotes();
		$I->createSomeWaitingQuotes(['nb_quotes' => $this->nbQuotes['waiting']]);
		$I->createSomePendingQuotes(['nb_quotes' => $this->nbQuotes['pending']]);

		$this->admin = $I->logANewUser(['security_level' => 1]);
	}

	public function clickOnLogoutOnProfile(FunctionalTester $I)
	{
		$I->am('a Teen Quotes\' administrator');
		$I->wantTo('view quotes waiting for moderation');

		$nbDaysToPublish = $this->computeNbDaysToPublishQuotes($this->nbQuotes['pending']);

		// Go to the admin panel
		$I->navigateToTheAdminPanel();

		// Check that counters are properly set
		$this->seeNumberOfWaitingQuotesIs($this->nbQuotes['waiting']);
		$this->seeNumberOfPendingQuotesIs($this->nbQuotes['pending']);
		$this->seeNumberOfDaysToPublishIs($nbDaysToPublish);
	}

	/**
	 * Compute the number of days to publish the given number of quotes
	 * @param  int $nbQuotes The number of quotes to publish
	 * @return int
	 */
	private function computeNbDaysToPublishQuotes($nbQuotes)
	{
		return ceil($nbQuotes / Config::get('app.quotes.nbQuotesToPublishPerDay'));
	}

	/**
	 * Assert that the number of waiting quotes is the given value
	 * @param  int $nb
	 */
	private function seeNumberOfWaitingQuotesIs($nb)
	{
		$this->tester->see($nb, '#nb-quotes-waiting');
	}

	/**
	 * Assert that the number days to publish quotes is the given value
	 * @param  int $nb
	 */
	private function seeNumberOfDaysToPublishIs($nb)
	{
		$this->tester->see($nb, '#nb-quotes-per-day');
	}

	/**
	 * Assert that the number of pending quotes is the given value
	 * @param  int $nb
	 */
	private function seeNumberOfPendingQuotesIs($nb)
	{
		$this->tester->see($nb, '#nb-quotes-pending');
	}
}