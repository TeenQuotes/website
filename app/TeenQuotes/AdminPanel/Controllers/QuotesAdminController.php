<?php namespace TeenQuotes\AdminPanel\Controllers;

use BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;
use QuoteNotFoundException;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Quotes\Models\Quote;

class QuotesAdminController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$quotes = Quote::waiting()
			->orderAscending()
			->get();

		$data = [
			'quotes'          => $quotes,
			'colors'          => Quote::getRandomColors(),
			'nbQuotesPending' => Quote::pending()->count(),
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
		$quote = Quote::waiting()
			->where('id', '=', $id)
			->with('user')
			->first();
		
		if (is_null($quote))
			throw new QuoteNotFoundException;

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
			$quote->approved = Quote::PENDING;
			$quote->save();

			// Contact the author of the quote
			// Send mail via SMTP
			new MailSwitcher('smtp');
			Mail::send('emails.quotes.approve', compact('quote'), function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quoteApproveSubjectEmail'));
			});

			return Redirect::route('admin.quotes.index')->with('success', 'The quote has been edited and approved!');
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
		$availableTypes = ['approve', 'unapprove', 'alert'];

		if ( ! in_array($type, $availableTypes))
			throw new InvalidArgumentException("Wrong type. Got ".$type.". Available values: ".implode('|', $availableTypes));

		if (Request::ajax()) {
			$quote = Quote::waiting()
				->where('id', '=', $id)
				->with('user')
				->first();

			// Handle quote not found
			if (is_null($quote))
				throw new InvalidArgumentException("Quote ".$id." is not a waiting quote.");

			$quote->approved = ($type == 'approve') ? Quote::PENDING : Quote::REFUSED;
			$quote->save();

			// Contact the author of the quote
			// Send mail via SMTP
			new MailSwitcher('smtp');
			Mail::send('emails.quotes.'.$type, compact('quote'), function($m) use($quote, $type)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quote'.ucfirst($type).'SubjectEmail'));
			});

			return Response::json(['success' => true], 200);
		}
	}
}