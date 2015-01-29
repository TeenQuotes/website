<?php namespace TeenQuotes\Api\V1\Controllers;

use Config, Input, URL;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;

class SearchController extends APIGlobalController implements PaginatedContentInterface {

	public function getSearch($query)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();

		// Get content
		list($quotes, $totalQuotes) = $this->getTotalQuotesAndQuotesForQuery($page, $pagesize, $query);
		list($users, $totalUsers) = $this->getTotalUsersAndUsersForQuery($page, $pagesize, $query);

		// Handle no results
		if ($totalQuotes == 0 AND $totalUsers == 0)
			throw new ApiNotFoundException('results');

		return Response::json([
			'quotes'       => $quotes->toArray(),
			'users'        => $users->toArray(),
			'total_quotes' => $totalQuotes,
			'total_users'  => $totalUsers,
			'pagesize'     => (int) $pagesize,
			'url'          => URL::current(),
		], 200, [], JSON_NUMERIC_CHECK);
	}

	public function getPagesize()
	{
		return Input::get('pagesize', Config::get('app.quotes.nbQuotesPerPage'));
	}

	/**
	 * Get the total number of quotes and these objects for a page,
	 * pagesize and query
	 *
	 * @param  int $page The page number
	 * @param  int $pagesize The pagesize
	 * @param  string $query The search query
	 * @return array The first element are the quotes, the second one is
	 * the total number of quotes
	 */
	private function getTotalQuotesAndQuotesForQuery($page, $pagesize, $query)
	{
		$quotes = $this->quoteRepo->searchPublishedWithQuery($query, $page, $pagesize);

		$totalQuotes = 0;
		// Don't search the total number of items if we have found nothing
		if ( ! $this->isNotFound($quotes))
			$totalQuotes = $this->quoteRepo->searchCountPublishedWithQuery($query);

		return [$quotes, $totalQuotes];
	}

	/**
	 * Get the total number of users and these objects for a page,
	 * pagesize and query
	 *
	 * @param  int $page The page number
	 * @param  int $pagesize The pagesize
	 * @param  string $query The search query
	 * @return array The first element are the users, the second one is
	 * the total number of users
	 */
	private function getTotalUsersAndUsersForQuery($page, $pagesize, $query)
	{
		$users = $this->userRepo->searchByPartialLogin($query, $page, $pagesize);

		$totalUsers = 0;
		// Don't search the total number of items if we have found nothing
		if ( ! $this->isNotFound($users))
			$totalUsers = $this->userRepo->countByPartialLogin($query);

		return [$users, $totalUsers];
	}
}