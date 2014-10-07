<?php

class SearchController extends BaseController {

	public function __construct()
	{
		$this->beforeFilter('search.isValid', ['only' => ['getResults', 'dispatcher']]);
	}

	/**
	 * Show the search form
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
	 * Show results after a search
	 * @var string $query The search query
	 * @return Response
	 */
	public function getResults($query)
	{
		$quotes = Quote::searchQuotes($query);

		$users = null;
		if ($this->stringIsASingleWord($query))
			$users = User::partialLogin($query)
				->notHidden()
				->with('countryObject')
				->get();

		// Handle no results
		if ($quotes->count() == 0 AND (is_null($users) OR $users->count() == 0))
			return Redirect::route('search.form')->with('warning', Lang::get('search.noResultsAtAll'));

		$data = [
			'quotes'                 => $quotes,
			'users'                  => $users,
			'query'                  => $query,
			'maxNbResultPerCategory' => Config::get('app.search.maxResultsPerCategory'),
			'pageTitle'              => Lang::get('search.resultsPageTitle', compact('query')),
			'pageDescription'        => Lang::get('search.resultsPageDescription', compact('query')),
		];

		return View::make('search.results', $data);
	}

	private function stringIsASingleWord($string)
	{
		return (str_word_count($string) == 1);
	}

	/**
	 * Dispatch the search form to search results
	 * @return Response
	 */
	public function dispatcher()
	{
		// filter search.isValid before
		return Redirect::route('search.results', array(Input::get('search')));
	}
}