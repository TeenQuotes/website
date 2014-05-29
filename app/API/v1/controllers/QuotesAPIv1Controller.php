<?php

class QuotesAPIv1Controller extends BaseController {

	public function getSingleQuote($quote_id)
	{
		$quote = Quote::whereId($quote_id)
		->with('comments')
		->with(array('comments.user' => function($query)
		{
		    $query->addSelect(array('id', 'login', 'avatar'));
		}))
		->with(array('user' => function($q)
		{
		    $q->addSelect(array('id', 'login', 'avatar'));
		}))
		->get();

		// Handle not found
		if (empty($quote) OR $quote->count() == 0) {

			$data = [
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found",
			];

			return Response::json($data, 404);
		}
		else
			return $quote;
	}

	public function indexFavoritesQuotes($user_id)
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.users.nbQuotesPerPage'));

        if ($page <= 0)
			$page = 1;

		$user = User::find($user_id);
		
		// Handle user not found
		if (is_null($user)) {
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found",
			];

			return Response::json($data, 400);
		}

		// Time to store list of favorites in cache
		$expiresAt = Carbon::now()->addMinutes(10);

		$arrayIDFavoritesQuotesForUser = Cache::remember(FavoriteQuote::$cacheNameFavoritesForUser.$user->id, $expiresAt, function() use ($user)
		{
			return FavoriteQuote::forUser($user)->select('quote_id')->get()->lists('quote_id');
		});

		$totalQuotes = count($arrayIDFavoritesQuotesForUser);
		
		// Get quotes
		$content = array();
		if ($totalQuotes > 0)
			$content = $this->getQuotesFavorite($page, $pagesize, $user, $arrayIDFavoritesQuotesForUser);

		// Handle no quotes found
		if (is_null($content) OR empty($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$data = $this->paginateQuotes($page, $pagesize, $totalQuotes, $content);
		
		return Response::json($data, 200);
	}

	public function indexByApprovedQuotes($quote_approved_type, $user_id)
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.users.nbQuotesPerPage'));

        if ($page <= 0)
			$page = 1;

		$user = User::find($user_id);
		
		// Handle user not found
		if (is_null($user)) {
			$data = [
				'status' => 'user_not_found',
				'error'  => "The user #".$user_id." was not found",
			];

			return Response::json($data, 400);
		}

		
		// Get quotes
		$content = $this->getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type);

		// Handle no quotes found
		$totalQuotes = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$totalQuotes = Quote::$quote_approved_type()->forUser($user)->count();

		$data = $this->paginateQuotes($page, $pagesize, $totalQuotes, $content);
		
		return Response::json($data, 200);
	}


	public function indexQuotes($random = null)
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.quotes.nbQuotesPerPage'));

        if ($page <= 0)
			$page = 1;

		$totalQuotes = Cache::rememberForever(Quote::$cacheNameNumberPublished, function()
		{
			return Quote::published()->count();
		});

        // Get quotes
        if (is_null($random))
        	$content = $this->getQuotesHome($page, $pagesize);
        else
        	$content = $this->getQuotesRandom($page, $pagesize);

		// Handle no quotes found
		if (is_null($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$data = $this->paginateQuotes($page, $pagesize, $totalQuotes, $content);
		
		return Response::json($data, 200);
	}

	private function paginateQuotes($page, $pagesize, $totalQuotes, $content)
	{
        $totalPages = ceil($totalQuotes / $pagesize);
		
		$data = [
			'quotes'       => $content->toArray(),
			'total_quotes' => $totalQuotes,
			'total_pages'  => $totalPages,
			'page'         => (int) $page,
			'pagesize'     => (int) $pagesize,
			'url'          => URL::current()
        ];

        // Add next page URL
        if ($page < $totalPages) {
        	$data['has_next_page'] = true;
        	$data['next_page'] = $data['url'].'?page='.($page + 1).'&pagesize='.$pagesize;
        }
        else
        	$data['has_next_page'] = false;

        // Add previous page URL
        if ($page >= 2) {
        	$data['has_previous_page'] = true;
        	$data['previous_page'] = $data['url'].'?page='.($page - 1).'&pagesize='.$pagesize;
        }
        else
        	$data['has_previous_page'] = false;

        return $data;
	}

	public function getSearch($query)
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.quotes.nbQuotesPerPage'));

        if ($page <= 0)
			$page = 1;
				
		// Get quotes
		$content = $this->getQuotesSearch($page, $pagesize, $query);

		// Handle no quotes found
		$totalQuotes = 0;
		if (is_null($content) OR empty($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

		$totalQuotes = Quote::
		// $query will NOT be bind here
		// it will be bind when calling setBindings
		whereRaw("MATCH(content) AGAINST(?)", array($query))
		->where('approved', '=', 1)
		// WARNING 1 corresponds to approved = 1
		// We need to bind it again
		->setBindings([$query, 1])
		->count();

		$data = $this->paginateQuotes($page, $pagesize, $totalQuotes, $content);
		
		return Response::json($data, 200);
	}

	public function postStoreQuote()
	{
		$user = User::find(ResourceServer::getOwnerId());
		$content = Input::get('content');
		$quotesSubmittedToday = Quote::createdToday()->forUser($user)->count();

		// Validate content of the quote
		$validatorContent = Validator::make(compact('content'), ['content' => Quote::$rulesAdd['content']]);
		if ($validatorContent->fails()) {
			$data = [
				'status' => 'wrong_content',
				'error'  => 'Content of the quote should be between 50 and 300 characters'
			];

			return Response::json($data, 400);
		}

		// Validate number of quotes submitted today
		$validatorNbQuotes = Validator::make(compact('quotesSubmittedToday'), ['quotesSubmittedToday' => Quote::$rulesAdd['quotesSubmittedToday']]);
		if ($validatorNbQuotes->fails()) {
			$data = [
				'status' => 'too_much_submitted_quotes',
				'error'  => "The maximum number of quotes you can submit is 5 per day"
			];

			return Response::json($data, 400);
		}

		// Store the quote
		$quote = new Quote;
		$quote->content = $content;
		$user->quotes()->save($quote);

		return Response::json($quote);
	}

	private function getQuotesHome($page, $pagesize)
	{
		// Time to store in cache
		$expiresAt = Carbon::now()->addMinutes(1);

        // Number of quotes to skip
        $skip = $pagesize * ($page - 1);

        if ($pagesize == Config::get('app.quotes.nbQuotesPerPage')) {

        	$content = Cache::remember(Quote::$cacheNameQuotesAPIPage.$page, $expiresAt, function() use($pagesize, $skip)
        	{
		        return Quote::published()
				->with(array('user' => function($q)
				{
				    $q->addSelect(array('id', 'login', 'avatar'));
				}))
				->orderDescending()
				->take($pagesize)
				->skip($skip)
				->get();
        	});
        }
        else {
        	$content = Quote::published()
				->with(array('user' => function($q)
				{
				    $q->addSelect(array('id', 'login', 'avatar'));
				}))
				->orderDescending()
				->take($pagesize)
				->skip($skip)
				->get();
        }

        return $content;
	}

	private function getQuotesRandom($page, $pagesize)
	{
		// Time to store in cache
		$expiresAt = Carbon::now()->addMinutes(1);

        // Number of quotes to skip
        $skip = $pagesize * ($page - 1);

        if ($pagesize == Config::get('app.quotes.nbQuotesPerPage')) {

        	$content = Cache::remember(Quote::$cacheNameRandomAPIPage.$page, $expiresAt, function() use($pagesize, $skip)
        	{
		        return Quote::published()
				->with(array('user' => function($q)
				{
				    $q->addSelect(array('id', 'login', 'avatar'));
				}))
				->random()
				->take($pagesize)
				->skip($skip)
				->get();
        	});
        }
        else {
        	$content = Quote::published()
				->with(array('user' => function($q)
				{
				    $q->addSelect(array('id', 'login', 'avatar'));
				}))
				->random()
				->take($pagesize)
				->skip($skip)
				->get();
        }

        return $content;
	}

	private function getQuotesFavorite($page, $pagesize, $user, $arrayIDFavoritesQuotesForUser)
	{
		// Number of quotes to skip
        $skip = $pagesize * ($page - 1);

		$content = Quote::whereIn('id', $arrayIDFavoritesQuotesForUser)
			->with(array('user' => function($q)
			{
			    $q->addSelect(array('id', 'login', 'avatar'));
			}))
			->take($pagesize)
			->skip($skip)
			->get();

		return $content;
	}

	private function getQuotesSearch($page, $pagesize, $query)
	{
		// Number of quotes to skip
        $skip = $pagesize * ($page - 1);

        $quotes = Quote::
		select('id', 'content', 'user_id', 'approved', 'created_at', 'updated_at', DB::raw("MATCH(content) AGAINST(?) AS `rank`"))
		// $search will NOT be bind here
		// it will be bind when calling setBindings
		->whereRaw("MATCH(content) AGAINST(?)", array($query))
		->where('approved', '=', 1)
		->orderBy('rank', 'DESC')
		->with(array('user' => function($q)
		{
		    $q->addSelect(array('id', 'login', 'avatar'));
		}))
		->skip($skip)
		->take($pagesize)
		// WARNING 1 corresponds to approved = 1
		// We need to bind it again
		->setBindings([$query, $query, 1])
		->get();

		return $quotes;
	}

	private function getQuotesByApprovedForUser($page, $pagesize, $user, $quote_approved_type)
	{
		// Number of quotes to skip
        $skip = $pagesize * ($page - 1);

		$content = Quote::$quote_approved_type()
			->with(array('user' => function($q)
			{
			    $q->addSelect(array('id', 'login', 'avatar'));
			}))
			->forUser($user)
			->orderDescending()
			->take($pagesize)
			->skip($skip)
			->get();

		return $content;
	}
}