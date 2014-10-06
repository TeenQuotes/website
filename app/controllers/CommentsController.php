<?php

use Illuminate\Http\Response as ResponseClass;

class CommentsController extends BaseController {

	/**
	 * The API controller
	 * @var TeenQuotes\Api\V1\Controllers\CommentsController
	 */
	private $api;

	public function __construct()
	{
		$this->beforeFilter('auth');
		$this->api = App::make('TeenQuotes\Api\V1\Controllers\CommentsController');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = [
			'content'  => Input::get('content'),
			'quote_id' => Input::get('quote_id'),
		];

		$validator = Validator::make($data, Comment::$rulesAdd);

		// Check if the form validates with success.
		if ($validator->passes()) {

			// Call the API - skip the API validator
			$response = $this->api->store($data['quote_id'], false);
			if ($response->getStatusCode() != 201)
				return Redirect::route('quotes.show', ['id' => $data['quote_id']])->withErrors($validator)->withInput(Input::all());

			return Redirect::route('quotes.show', ['id' => $data['quote_id']])->with('success', Lang::get('comments.commentAddedSuccessfull'));
		}

		// Something went wrong.
		return Redirect::route('quotes.show', ['id' => $data['quote_id']])->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (Request::ajax()) {

			$response = $this->api->destroy($id);
			
			return Response::json([
				'success' => ($response->getStatusCode() == ResponseClass::HTTP_OK)
			]);
		}
	}

}