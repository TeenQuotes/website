<?php

class APIv1Controller extends BaseController {

	public function showWelcome()
	{
		$data = [
			'status'            => 'You have arrived',
			'message'           => 'Welcome to the Teen Quotes API',
			'version'           => '1.0alpha',
			'url_documentation' => 'https://github.com/TeenQuotes/api-documentation',
			'contact'           => 'antoine.augusti@teen-quotes.com',
		];

		return Response::json($data, 200);
	}

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

	public function postFavorite($quote_id)
	{
		$user = User::find(ResourceServer::getOwnerId());

		$validatorQuote = Validator::make(compact('quote_id'), ['quote_id' => FavoriteQuote::$rulesAddFavorite['quote_id']]);
		if ($validatorQuote->fails()) {
			$data = [
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found",
			];

			return Response::json($data, 400);
		}

		// Try to find if the user has this quote in favorite from cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			$alreadyFavorited = in_array($quote_id, Cache::get(FavoriteQuote::$cacheNameFavoritesForUser.$user->id));
		else {
			$favorite = FavoriteQuote::where('quote_id', '=' , $quote_id)->where('user_id', '=' , $user->id)->count();
			$alreadyFavorited = ($favorite == 1);
		}

		if ($alreadyFavorited) {
			$data = [
				'status' => 'quote_already_favorited',
				'error'  => "The quote #".$quote_id." was already favorited",
			];

			return Response::json($data, 400);
		}

		// Store the favorite
		$favorite = new FavoriteQuote;
		$favorite->user_id = $user->id;
		$favorite->quote_id = $quote_id;
		$favorite->save();

		// Delete the cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$user->id);

		// If we have the number of favorites in cache, increment it
		if (Cache::has(Quote::$cacheNameNbFavorites.$quote_id))
			Cache::increment(Quote::$cacheNameNbFavorites.$quote_id);

		return Response::json($favorite, 200);
	}

	public function deleteFavorite($quote_id)
	{
		$user = User::find(ResourceServer::getOwnerId());

		$validatorFavoriteQuote = Validator::make(compact('quote_id'), ['quote_id' => 'exists:favorite_quotes,quote_id,user_id,'.$user->id]);
		if ($validatorFavoriteQuote->fails()) {
			$data = [
				'status' => 'quote_not_found',
				'error'  => "The quote #".$quote_id." was not found",
			];

			return Response::json($data, 400);
		}

		// Delete the FavoriteQuote from database
		FavoriteQuote::where('quote_id', '=' , $quote_id)->where('user_id', '=' , $user->id)->delete();

		// Delete the cache
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$user->id);

		// If we have the number of favorites in cache, decrement it
		if (Cache::has(Quote::$cacheNameNbFavorites.$quote_id))
			Cache::decrement(Quote::$cacheNameNbFavorites.$quote_id);

		$data = [
			'status' => 'favorite_deleted',
			'success'  => "The quote #".$quote_id." was deleted from favorites",
		];

		return Response::json($data, 200);
	}

	public function postUsers()
	{
		$data = [
			'login'    => Input::get('login'),
			'password' => Input::get('password'),
			'email'    => Input::get('email'),
		];

		// Validate login
		$validatorLogin = Validator::make(['login' => $data['login']], ['login' => User::$rulesSignup['login']]);
		if ($validatorLogin->fails()) {
			$data = [
				'status' => 'wrong_login',
				'error'  => $validatorLogin->messages()->first('login'),
			];

			return Response::json($data, 400);
		}

		// Validate password
		$validatorPassword = Validator::make(['password' => $data['password']], ['password' => User::$rulesSignup['password']]);
		if ($validatorPassword->fails()) {
			$data = [
				'status' => 'wrong_password',
				'error'  => $validatorPassword->messages()->first('password'),
			];

			return Response::json($data, 400);
		}

		// Validate email
		$validatorEmail = Validator::make(['email' => $data['email']], ['email' => User::$rulesSignup['email']]);
		if ($validatorEmail->fails()) {
			$data = [
				'status' => 'wrong_email',
				'error'  => $validatorEmail->messages()->first('email'),
			];

			return Response::json($data, 400);
		}

		// Store the new user
		$user             = new User;
		$user->login      = $data['login'];
		$user->email      = $data['email'];
		$user->password   = Hash::make($data['password']);
		$user->ip         = $_SERVER['REMOTE_ADDR'];
		$user->last_visit = Carbon::now()->toDateTimeString();
		$user->save();

		// Subscribe the user to the weekly newsletter
		Newsletter::createNewsletterForUser($user, 'weekly');

		// Send the welcome email
		Mail::send('emails.welcome', $data, function($m) use($data)
		{
			$m->to($data['email'], $data['login'])->subject(Lang::get('auth.subjectWelcomeEmail'));
		});

		return Response::json($user, 200);
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

	public function putPassword()
	{
		$user = User::find(ResourceServer::getOwnerId());

		$data = [
			'password'              => Input::get('password'),
			'password_confirmation' => Input::get('password_confirmation'),
		];

		$validatorPassword = Validator::make($data, User::$rulesUpdatePassword);

		// Validate password
		if ($validatorPassword->fails()) {
			$data = [
				'status' => 'wrong_password',
				'error'  => $validatorPassword->messages()->first('password'),
			];

			return Response::json($data, 400);
		}

		// Update new password
		$user->password = Hash::make($data['password']);
		$user->save();

		$data = [
			'status'  => 'password_updated',
			'success' => 'The new password has been set.',
		];

		return Response::json($data, 200);
	}

	public function getCountry($country_id = null)
	{
		if (is_null($country_id))
			return Country::all();
		
		$country = Country::find($country_id);

		if (is_null($country)) {
			$data = [
				'status' => 'country_not_found',
				'error'  => "The country #".$country_id." was not found",
			];

			return Response::json($data, 404);
		}

		return Response::json($country, 200);
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