<?php

class QuotesAdminController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$quotes = Quote::waiting()->orderAscending()->get();

		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(),
			'nbQuotesPending' => Quote::pending()->count(),
			'nbQuotesPerDay'  => Config::get('app.quotes.nbQuotesToPublishPerDay'),
			'pageTitle'       => 'Admin | '.Lang::get('layout.nameWebsite'),
		];

		// Put variables that we will use in JavaScript
		JavaScript::put([
			'nbQuotesPerDay' => Config::get('app.quotes.nbQuotesToPublishPerDay'),
			'quotesPlural'   => Lang::choice('quotes.quotesText', 2),
			'daysPlural'     => Lang::choice('quotes.daysText', 2),
    	]);

		return View::make('admin.index', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id The ID of the quote that we want to edit
	 * @return Response
	 */
	public function edit($id)
	{
		$quote = Quote::find($id);
		if (is_null($quote))
			throw new QuoteNotFoundException;

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
		$quote = Quote::waiting()->where('id', '=', $id)->with('user')->first();
		if (is_null($quote))
			App::abort(404, "Can't find quote ".$id);

		$data = [
			'content'              => Input::get('content'),
			// Just to use the same validation rules
			'quotesSubmittedToday' => 0,
		];

		$validator = Validator::make($data, Quote::$rulesAdd);

		// Check if the form validates with success.
		if ($validator->passes()) {

			// Update the quote
			$quote->content = $data['content'];
			$quote->approved = 2;
			$quote->save();

			// Contact the author of the quote
			$quoteArray = array('quote' => $quote->toArray());

			// Send mail via SMTP
			new MailSwitcher('smtp');
			Mail::send('emails.quotes.approve', $quoteArray, function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quoteApproveSubjectEmail'));
			});

			return Redirect::route('admin.quotes.index')->with('success', Lang::get('The quote has been edited and approved!'));
		}

		// Something went wrong.
		return Redirect::back()->withErrors($validator)->withInput(Input::all());
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
		if (!in_array($type, array('approve', 'unapprove')))
			throw new InvalidArgumentException("Expected type approve or unapprove. Got ".$type);

		if (Request::ajax()) {
			$quote = Quote::waiting()->where('id', '=', $id)->with('user')->first();

			// Handle quote not found
			if (is_null($quote))
				throw new InvalidArgumentException("Quote ".$id." is not a waiting quote.");

			$quote->approved = ($type == 'approve') ? 2 : -1;
			$quote->save();

			// Contact the author of the quote
			$quoteArray = array('quote' => $quote->toArray());
			Mail::send('emails.quotes.'.$type, $quoteArray, function($m) use($quote, $type)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quote'.ucfirst($type).'SubjectEmail'));
			});

			return Response::json(['success' => true], 200);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}