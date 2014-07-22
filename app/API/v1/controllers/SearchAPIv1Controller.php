<?php

class SearchAPIv1Controller extends BaseController {
	
	public function getSearch($query)
	{
		$page = 1;
		$pagesize = Input::get('pagesize', Config::get('app.quotes.nbQuotesPerPage'));

        if ($page <= 0)
			$page = 1;
				
		// Get content
		$quotes = App::make('QuotesAPIv1Controller')->getQuotesSearch($page, $pagesize, $query);
		$users = App::make('UsersAPIv1Controller')->getUsersSearch($page, $pagesize, $query);

		$totalQuotes = 0;
		$totalUsers = 0;

		if (!is_null($quotes) AND !empty($quotes) AND $quotes->count() > 0) {
			$totalQuotes = Quote::
			// $query will NOT be bind here
			// it will be bind when calling setBindings
			whereRaw("MATCH(content) AGAINST(?)", array($query))
			->where('approved', '=', 1)
			// WARNING 1 corresponds to approved = 1
			// We need to bind it again
			->setBindings([$query, 1])
			->count();
		}

		if (!is_null($users) AND !empty($users) AND $users->count() > 0)
			$totalUsers = User::partialLogin($query)->notHidden()->count();

		// Handle no results
		if ($totalQuotes == 0 AND $totalUsers == 0) {
			$data = [
				'status' => 404,
				'error'  => 'No results have been found.'
			];

			return Response::json($data, 404);
		}

		$data = [
			'quotes'       => $quotes->toArray(),
			'users'        => $users->toArray(),
			'total_quotes' => $totalQuotes,
			'total_users'  => $totalUsers,
			'pagesize'     => (int) $pagesize,
			'url'          => URL::current(),
		];

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}
}