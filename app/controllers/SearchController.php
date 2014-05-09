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
		return View::make('search.form');
	}

	/**
	 * @brief Show results after a search
	 * @var string $query The search query
	 * @return Response
	 */
	public function getResults($query)
	{
		// filter search.isValid before
		return Quote::searchQuotes($query);
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