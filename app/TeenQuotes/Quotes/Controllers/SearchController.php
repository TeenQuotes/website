<?php namespace TeenQuotes\Quotes\Controllers;

use BaseController, Config, Input, Lang, Redirect, View;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Users\Repositories\UserRepository;

class SearchController extends BaseController {

	/**
	 * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var \TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	public function __construct(QuoteRepository $quoteRepo, UserRepository $userRepo)
	{
		$this->beforeFilter('search.isValid', ['only' => ['getResults', 'dispatcher']]);
		$this->quoteRepo = $quoteRepo;
		$this->userRepo = $userRepo;
	}

	/**
	 * Show the search form
	 * @return \Response
	 */
	public function showForm()
	{
		$data = [
			'pageTitle'       => Lang::get('search.formPageTitle'),
			'pageDescription' => Lang::get('search.formPageDescription'),
		];

		return View::make('search.form', $data);
	}

	/**
	 * Show results after a search
	 * @var string $query The search query
	 * @return \Response
	 */
	public function getResults($query)
	{
		// Search quotes
		$nbQuotes = $this->quoteRepo->searchCountPublishedWithQuery($query);
		$quotes = $this->quoteRepo->searchPublishedWithQuery($query, 1, Config::get('app.search.maxResultsPerCategory'));

		// Search users
		$nbUsers = 0;
		$users = null;
		if ($this->stringIsASingleWord($query)) {
			$nbUsers = $this->userRepo->countByPartialLogin($query);
			$users = $this->userRepo->searchByPartialLogin($query, 1, Config::get('app.search.maxResultsPerCategory'));
		}

		// Handle no results
		if ($quotes->count() == 0 AND (is_null($users) OR $users->count() == 0))
			return Redirect::route('search.form')->with('warning', Lang::get('search.noResultsAtAll'));

		$data = compact('quotes', 'users', 'nbQuotes', 'nbUsers', 'query');
		$data['maxNbResultPerCategory'] = Config::get('app.search.maxResultsPerCategory');
		$data['pageTitle']              = Lang::get('search.resultsPageTitle', compact('query'));
		$data['pageDescription']        = Lang::get('search.resultsPageDescription', compact('query'));

		return View::make('search.results', $data);
	}

	private function stringIsASingleWord($string)
	{
		return (str_word_count($string) == 1);
	}

	/**
	 * Dispatch the search form to search results
	 * @return \Response
	 */
	public function dispatcher()
	{
		// filter search.isValid before
		return Redirect::route('search.results', Input::get('search'));
	}
}