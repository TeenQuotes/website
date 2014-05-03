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
			'quotes'    => $quotes,
			'colors'    => Quote::getRandomColors(),

			'pageTitle' => 'Admin | '.Lang::get('layout.nameWebsite'),
		];

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
			App::abort(404, "Can't find quote ".$id);

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
		$quote = Quote::find($id);
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

			// TODO: contact the author of the quote

			return Redirect::route('admin.quotes.index')->with('success', Lang::get('The quote has been edited and approved!'));
		}

		// Something went wrong.
		return Redirect::back()->withErrors($validator)->withInput(Input::all());
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