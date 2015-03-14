<?php

use TeenQuotes\AdminPanel\Helpers\Moderation;
use TeenQuotes\Quotes\Models\Quote;

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

	/**
	 * Quotes that are waiting to be published
	 * @var array
	 */
	private $waitingQuotes;

	public function _before(FunctionalTester $I)
	{
		$this->tester = $I;

		$this->nbQuotes = [
			'pending' => 7,
			'waiting' => 26,
		];

		$I->createSomePublishedQuotes();
		$this->waitingQuotes = $I->createSomeWaitingQuotes(['nb_quotes' => $this->nbQuotes['waiting']]);
		$I->createSomePendingQuotes(['nb_quotes' => $this->nbQuotes['pending']]);

		$this->admin = $I->logANewUser(['security_level' => 1]);
	}

	public function clickOnLogoutOnProfile(FunctionalTester $I)
	{
		$I->am("a Teen Quotes' administrator");
		$I->wantTo('view quotes waiting for moderation');

		$nbDaysToPublish = $this->computeNbDaysToPublishQuotes($this->nbQuotes['pending']);

		// Go to the admin panel
		$I->navigateToTheAdminPanel();

		// Check that counters are properly set
		$this->seeNumberOfWaitingQuotesIs($this->nbQuotes['waiting']);
		$this->seeNumberOfPendingQuotesIs($this->nbQuotes['pending']);
		$this->seeNumberOfDaysToPublishIs($nbDaysToPublish);

		$this->seeRequiredElementsForQuotes($this->waitingQuotes);
	}

	/**
	 * I can see that quotes have things properly being displayed
	 * @param  array  $quotes
	 */
	private function seeRequiredElementsForQuotes(array $quotes)
	{
		foreach ($this->waitingQuotes as $quote)
		{
			$this->seeModerationButtonsForQuote($quote);
			$this->seeContentForQuote($quote);
		}
	}

	/**
	 * I can see that the content of a quote is displayed
	 * @param  \TeenQuotes\Quotes\Models\Quote  $q
	 */
	private function seeContentForQuote(Quote $q)
	{
		$parentClass = $this->getCssParentClass($q);

		$this->tester->see($q->content, $parentClass);
	}

	/**
	 * I can see moderation buttons for each quote
	 * @param  \TeenQuotes\Quotes\Models\Quote  $q
	 */
	private function seeModerationButtonsForQuote(Quote $q)
	{
		$parentClass = $this->getCssParentClass($q);

		// I can see moderation decisions
		$moderationDecisions = Moderation::getAvailableTypes();
		foreach ($moderationDecisions as $decision)
		{
			$cssClass = $parentClass.' .quote-moderation[data-decision="'.$decision.'"]';
			$this->tester->seeNumberOfElements($cssClass, 1);
		}

		// I can see the edit button
		$this->tester->seeNumberOfElements($parentClass.' .fa-pencil-square-o', 1);
	}

	/**
	 * Get the CSS class for a quote
	 * @param  \TeenQuotes\Quotes\ModelsQuote  $q
	 */
	private function getCssParentClass(Quote $q)
	{
		return '.quote[data-id='.$q->id.']';
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