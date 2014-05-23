<?php

class APIv1Controller extends BaseController {

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
				'status' => 404,
				'error' => 'Quote not found.'
			];

			return Response::json($data, 404);
		}
		else
			return $quote;
	}

	public function getSingleUser($user_id)
	{
		$user = User::where('login', '=', $user_id)
		->orWhere('id', '=', $user_id)
		->with(array('countryObject' => function($q)
		{
			$q->addSelect(array('id', 'name'));
		}))
		->with(array('newsletters' => function($q)
		{
			$q->addSelect('user_id', 'type', 'created_at');
		}))
		->first();

		// User not found
		if (empty($user) OR $user->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'User not found.'
			];

			return Response::json($data, 404);
		}

		$data = $user->toArray();
		foreach (User::$appendsFull as $key) {
			$method = Str::camel('get_'.$key);
			$data[$key] = $user->$method();
		}

		return Response::json($data);
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
        $totalPages = ceil($totalQuotes / $pagesize);

        // Get quotes
        if (is_null($random))
        	$content = $this->getQuotesHome($page, $pagesize);
        else
        	$content = $this->getQuotesRandom($page, $pagesize);

		// Handle not found
		if (is_null($content) OR $content->count() == 0) {

			$data = [
				'status' => 404,
				'error' => 'No quotes have been found.'
			];

			return Response::json($data, 404);
		}

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

		return Response::json($data);
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
}