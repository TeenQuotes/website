<?php

class QuotesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Auth::attempt(array('login' => 'antoineaugusti', 'password' => '1234'));

		// Random quotes or not?
		if (Route::currentRouteName() != 'random')
			$quotes = Quote::published()->with('user')->orderBy('created_at', 'DESC')->paginate(10);
		else
			$quotes = Quote::published()->with('user')->orderBy(DB::raw('RAND()'))->paginate(10);

		$data = [
			'quotes' => $quotes,
			'pageTitle' => 'Homepage',
			'colors' => Quote::getRandomColors(),
		];

		return View::make('quotes.index', $data);
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
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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