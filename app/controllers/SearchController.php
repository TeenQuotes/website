<?php

class SearchController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('search.isValid', array('only' => array('getResults', 'dispatcher')));
	}

	/**
	 * @brief Show the search form
	 * @return Response
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
	 * @brief Show results after a search
	 * @var string $query The search query
	 * @return Response
	 */
	public function getResults($query)
	{
		// filter search.isValid before
		$quotes = Quote::searchQuotes($query);

		if (str_word_count($query) == 1)
			$users = User::partialLogin($query)->notHidden()->with('countryObject')->get();
		else
			$users = null;

		// Handle no results
		if ($quotes->count() == 0 AND (is_null($users) OR $users->count() == 0))
			return Redirect::route('search.form')->with('warning', Lang::get('search.noResultsAtAll'));

		$data = [
			'quotes'                 => $quotes,
			'users'                  => $users,
			'query'                  => $query,
			'maxNbResultPerCategory' => QuotesController::$nbQuotesPerPage,
			'colors'                 => Quote::getRandomColors(),
			'pageTitle'              => Lang::get('search.resultsPageTitle'),
			'pageDescription'        => Lang::get('search.resultsPageDescription'),
		];

		// return $users;

		return View::make('search.results', $data);
	}

	/**
	 * @brief Dispatch the search form to search results
	 * @return Response
	 */
	public function dispatcher()
	{
		// filter search.isValid before
		return Redirect::route('search.results', array(Input::get('search')));
	}
}