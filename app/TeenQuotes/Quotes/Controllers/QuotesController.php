<?php namespace TeenQuotes\Quotes\Controllers;

use BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Paginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use TeenQuotes\Exceptions\QuoteNotFoundException;
use TeenQuotes\Http\JsonResponse;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class QuotesController extends BaseController {

	/**
	 * The API controller
	 * @var TeenQuotes\Api\V1\Controllers\QuotesController
	 */
	private $api;

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var TeenQuotes\Quotes\Validation\QuoteValidator
	 */
	private $quoteValidator;

	public function __construct(QuoteRepository $quoteRepo)
	{
		$this->beforeFilter('auth', ['on' => 'store']);
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\QuotesController');
		$this->quoteRepo = $quoteRepo;
		$this->quoteValidator = App::make('TeenQuotes\Quotes\Validation\QuoteValidator');
	}

	/**
	 * Redirect to the new URL type
	 * @param  int $id ID of the Quote
	 * @return Response
	 */
	public function redirectOldUrl($id)
	{
		return Redirect::route('quotes.show', $id, 301);
	}

	/**
	 * Display a bunch of quotes
	 *
	 * @return Response
	 */
	public function index()
	{		
		// Random quotes or not?
		$response = (Route::currentRouteName() == 'random') ? $this->retrieveRandomQuotes() : $this->retrieveLastQuotes();

		// Grab quotes
		$quotes = $response['quotes'];

		$data = [
			'quotes'          => $quotes,
			'pageTitle'       => Lang::get('quotes.'.Route::currentRouteName().'PageTitle'),
			'pageDescription' => Lang::get('quotes.'.Route::currentRouteName().'PageDescription'),
			'paginator'       => Paginator::make($quotes->toArray(), $response['total_quotes'], $response['pagesize']),
		];

		return View::make('quotes.index', $data);
	}

	private function retrieveLastQuotes()
	{
		$apiResponse = $this->api->index();
		
		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	private function retrieveRandomQuotes()
	{
		$apiResponse = $this->api->random();
		
		$this->guardAgainstNotFound($apiResponse);

		return $apiResponse->getOriginalData();
	}

	/**
	 * Throw an exception if the given response is a not found response
	 * @param  JsonResponse $response the response
	 * @return void|TeenQuotes\Exceptions\QuoteNotFoundException
	 */
	private function guardAgainstNotFound(JsonResponse $response)
	{
		if ($this->responseIsNotFound($response))
			throw new QuoteNotFoundException;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = Auth::user();

		$data = [
			'content'              => Input::get('content'),
			'quotesSubmittedToday' => $this->quoteRepo->submittedTodayForUser($user),
		];

		$this->quoteValidator->validatePosting($data);

		// Call the API to store the quote
		$response = $this->api->store(false);
		if ($response->getStatusCode() == 201)
			return Redirect::route('home')->with('success', Lang::get('quotes.quoteAddedSuccessfull', ['login' => $user->login]));
		
		App::abort(500, "Can't create quote.");
	}

	/**
	 * Display the form to add a quote
	 *
	 * @return Response
	 */
	public function create()
	{
		$data = [
			'pageTitle'       => Lang::get('quotes.addquotePageTitle'),
			'pageDescription' => Lang::get('quotes.addquotePageDescription'),
		];

		// JS variables are set in a view composer

		return View::make('quotes.addquote', $data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$response = $this->api->show($id);

		$this->guardAgainstNotFound($response);
		
		$quote = $response->getOriginalData(); 

		// If the user was not logged in, we store the current URL in its session
		// After sign in / sign up, he will be redirected here
		if (Auth::guest())
			Session::put('url.intended', URL::route('quotes.show', $id));

		$data = [
			'quote'           => $quote,
			'pageTitle'       => Lang::get('quotes.singleQuotePageTitle', compact('id')),
			'pageDescription' => $quote->content,
		];

		// JS variables and colors are set in a view composer

		return View::make('quotes.show', $data);
	}

	public function getDataFavoritesInfo()
	{
		$quote = $this->quoteRepo->getById(Input::get('id'));
		$data = $quote->present()->favoritesData;

		$translate = Lang::choice('quotes.favoritesText', $data['nbFavorites'], $data);

		return Response::json(compact('translate'), 200);
	}
}