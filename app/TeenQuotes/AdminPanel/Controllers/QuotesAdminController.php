<?php namespace TeenQuotes\AdminPanel\Controllers;

use App, Config, Input, Lang, Redirect, Request, Response, View;
use BaseController;
use InvalidArgumentException;
use TeenQuotes\Exceptions\QuoteNotFoundException;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class QuotesAdminController extends BaseController {

	/**
	 * @var TeenQuotes\Quotes\Repositories\QuoteRepository
	 */
	private $quoteRepo;

	/**
	 * @var TeenQuotes\Quotes\Validation\QuoteValidator
	 */
	private $quoteValidator;

	/**
	 * @var TeenQuotes\Mail\UserMailer
	 */
	private $userMailer;

	function __construct(QuoteRepository $quoteRepo, UserMailer $userMailer)
	{
		$this->quoteRepo = $quoteRepo;
		$this->quoteValidator = App::make('TeenQuotes\Quotes\Validation\QuoteValidator');
		$this->userMailer = $userMailer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$quotes = $this->quoteRepo->lastWaitingQuotes();

		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(),
			'nbQuotesPending' => $this->quoteRepo->nbPending(),
			'nbQuotesPerDay'  => Config::get('app.quotes.nbQuotesToPublishPerDay'),
			'pageTitle'       => 'Admin | '.Lang::get('layout.nameWebsite'),
		];

		// Bind JS variables to the view in a view composer

		return View::make('admin.index', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id The ID of the quote that we want to edit
	 * @return Response
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
	 * @return Response
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
		$this->sendMailForQuoteAndApprove($quote, 'approve');

		return Redirect::route('admin.quotes.index')->with('success', 'The quote has been edited and approved!');
	}

	/**
	 * Moderate a quote
	 *
	 * @param  int  $id The ID of the quote
	 * @param  string $type The decision of the moderation: approve|unapprove
	 * @warning Should be called using Ajax
	 * @return Response
	 */
	public function postModerate($id, $type)
	{
		$this->guardType($type);

		if (Request::ajax()) {
			$quote = $this->quoteRepo->waitingById($id);

			// Handle quote not found
			if (is_null($quote))
				throw new InvalidArgumentException("Quote ".$id." is not a waiting quote.");

			$approved = ($type == 'approve') ? Quote::PENDING : Quote::REFUSED;
			$quote = $this->quoteRepo->updateApproved($id, $approved);

			// Contact the author of the quote
			$this->sendMailForQuoteAndApprove($quote, $type);

			return Response::json(['success' => true], 200);
		}
	}

	private function guardType($type)
	{
		if ( ! in_array($type, $this->getAvailableTypes()))
			throw new InvalidArgumentException("Wrong type. Got ".$type.". Available values: ".$this->presentAvailableTypes());
	}

	private function getAvailableTypes()
	{
		return ['approve', 'unapprove', 'alert'];
	}

	private function presentAvailableTypes()
	{
		return implode('|', $this->getAvailableTypes());
	}

	private function sendMailForQuoteAndApprove($quote, $type)
	{
		$this->userMailer->send('emails.quotes.'.$type,
			$quote->user,
			compact('quote'),
			Lang::get('quotes.quote'.ucfirst($type).'SubjectEmail')
		);
	}
}