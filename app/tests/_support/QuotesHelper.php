<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Quote;

class QuotesHelper extends Module {

	public function getNbComments()
	{
		return Config::get('app.comments.nbCommentsPerPage');
	}

	public function getTotalNumberOfQuotesToCreate()
	{
		return $this->getNbPagesToCreate() * $this->getNbQuotesPerPage();
	}

	public function getNbPagesToCreate()
	{
		return 3;
	}

	public function getNbQuotesPerPage()
	{
		return Config::get('app.quotes.nbQuotesPerPage');
	}

	public function submitANewQuote()
	{
		$I = $this->getModule('Laravel4');

		$this->getModule('NavigationHelper')->navigateToTheAddQuotePage();

		$oldNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		$this->getModule('FormFillerHelper')->fillAddQuoteForm();

		$I->amOnRoute('home');
		$this->getModule('FunctionalHelper')->seeSuccessFlashMessage('Your quote has been submitted');

		$currentNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		// Assert that the quote was added to the DB
		$I->assertEquals($oldNbWaitingQuotes + 1, $currentNbWaitingQuotes);
	}

	public function cantSubmitANewQuote()
	{
		$I = $this->getModule('Laravel4');
		
		$this->getModule('NavigationHelper')->navigateToTheAddQuotePage();
		
		$oldNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		$this->getModule('FormFillerHelper')->fillAddQuoteForm();
		
		$I->amOnRoute('addquote');
		$I->see('You have submitted enough quotes for today');

		$currentNbWaitingQuotes = $this->numberWaitingQuotesForUser();
		
		// Assert that the quote was not added to the DB
		$I->assertEquals($oldNbWaitingQuotes, $currentNbWaitingQuotes);
	}

	/**
	 * Add a quote to the favorites of a user
	 * @param int $quote_id The ID of the quote
	 * @param int $user_id  The user ID
	 */
	public function addAFavoriteForUser($quote_id, $user_id)
	{
		return $this->getModule('FunctionalHelper')->insertInDatabase(1, 'FavoriteQuote', ['quote_id' => $quote_id, 'user_id' => $user_id]);
	}

	/**
	 * Count the number of quotes waiting moderation for a user
	 * @param  User $u The user. If null, use the authenticated user
	 * @return int The number of quotes waiting moderation for the user
	 */
	public function numberWaitingQuotesForUser(User $u = null)
	{
		if (is_null($u))
			$u = Auth::user();

		return Quote::forUser($u)
			->waiting()
			->count();
	}

	/**
	 * Create some published quotes
	 * @param  array $overrides The key-value array used to override dummy values. If the key nb_quotes is given, specifies the number of quotes to create
	 * @return array The created quotes
	 */
	public function createSomePublishedQuotes($overrides = [])
	{
		$overrides['approved'] = Quote::PUBLISHED;

		if (array_key_exists('nb_quotes', $overrides)) {
			$nbQuotes = $overrides['nb_quotes'];
			unset($overrides['nb_quotes']);
		}
		else
			$nbQuotes = 10;
		
		return $this->getModule('FunctionalHelper')->insertInDatabase($nbQuotes, 'Quote', $overrides);
	}
}