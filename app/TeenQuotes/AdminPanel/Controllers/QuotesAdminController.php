<?php namespace TeenQuotes\AdminPanel\Controllers;

use App, BaseController, Config, Input, InvalidArgumentException, Lang;
use Redirect, Request, Response, View;
use TeenQuotes\AdminPanel\Helpers\Moderation;
use TeenQuotes\Exceptions\QuoteNotFoundException;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class QuotesAdminController extends BaseController {

	/**
	 * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var \TeenQuotes\Quotes\Validation\QuoteValidator
	 */
	private $quoteValidator;

	/**
	 * @var \TeenQuotes\Mail\UserMailer
	 */
	private $userMailer;

	function __construct(QuoteRepository $quoteRepo, UserMailer $userMailer)
	{
		$this->quoteRepo      = $quoteRepo;
		$this->quoteValidator = App::make('TeenQuotes\Quotes\Validation\QuoteValidator');
		$this->userMailer     = $userMailer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Response
	 */
	public function index()
	{
		$quotes = $this->quoteRepo->lastWaitingQuotes();

		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(),
			'nbQuotesPending' => $this->quoteRepo->nbPending(),
			'nbQuotesPerDay'  => Config::get('app.quotes.nbQuotesToPublishPerDay'),
		];

		return View::make('admin.index', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id The ID of the quote that we want to edit
	 * @return \Response
	 */
	public function edit($id)
	{
		$quote = $this->quoteRepo->waitingById($id);

		if (is_null($quote)) throw new QuoteNotFoundException;

		return View::make('admin.edit')->withQuote($quote);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id The ID of the quote we want to edit
	 * @return \Response
	 */
	public function update($id)
	{
		$quote = $this->quoteRepo->waitingById($id);

		if (is_null($quote)) throw new QuoteNotFoundException;

		$data = [
			'content'              => Input::get('content'),
			// Just to use the same validation rules
			'quotesSubmittedToday' => 0,
		];

		$this->quoteValidator->validatePosting($data);

		// Update the quote
		$quote = $this->quoteRepo->updateContentAndApproved($id, $data['content'], Quote::PENDING);

		// Contact the author of the quote
		$this->sendMailForQuoteAndModeration($quote, new Moderation('approve'));

		return Redirect::route('admin.quotes.index')->with('success', 'The quote has been edited and approved!');
	}

	/**
	 * Moderate a quote
	 *
	 * @param  int  $id The ID of the quote
	 * @param  string $type The decision of the moderation: approve|unapprove
	 * @warning Should be called using Ajax
	 * @return \Response
	 */
	public function postModerate($id, $type)
	{
		$moderation = new Moderation($type);

		if (Request::ajax())
		{
			$quote = $this->quoteRepo->waitingById($id);

			// Handle quote not found
			if (is_null($quote))
				throw new InvalidArgumentException("Quote ".$id." is not a waiting quote.");

			$approved = $moderation->isApproved() ? Quote::PENDING : Quote::REFUSED;
			$quote = $this->quoteRepo->updateApproved($id, $approved);

			// Contact the author of the quote
			$this->sendMailForQuoteAndModeration($quote, $moderation);

			return Response::json(['success' => true], 200);
		}
	}

	/**
	 * Send an email to the author of quote telling the moderation decision
	 * @param  \TeenQuotes\Quotes\Models\Quote $quote
	 * @param  \TeenQuotes\AdminPanel\Helpers\Moderation $moderation The moderation decision
	 */
	private function sendMailForQuoteAndModeration($quote, Moderation $moderation)
	{
		$nbDays = 0;
		// Retrieve the number of days before the publication of the quote
		if ($moderation->isApproved())
			$nbDays = $this->quoteRepo->nbDaysUntilPublication($quote);

		$this->userMailer->sendModeration($moderation, $quote, $nbDays);
	}
}