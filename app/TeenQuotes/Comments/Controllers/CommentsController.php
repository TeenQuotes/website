<?php namespace TeenQuotes\Comments\Controllers;

use BaseController;
use Illuminate\Http\Response as ResponseClass;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use TeenQuotes\Comments\Models\Comment;

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
		$data = Input::only('content', 'quote_id');

		// Check if the form validates with success
		$validator = Validator::make($data, Comment::$rulesAdd);
		if ($validator->passes()) {

			// Call the API - skip the API validator
			$response = $this->api->store($data['quote_id'], false);
			if ($response->getStatusCode() != 201)
				return Redirect::route('quotes.show', $data['quote_id'])->withErrors($validator)->withInput(Input::all());

			return Redirect::route('quotes.show', $data['quote_id'])->with('success', Lang::get('comments.commentAddedSuccessfull'));
		}

		// Something went wrong
		return Redirect::route('quotes.show', $data['quote_id'])->withErrors($validator)->withInput(Input::all());
	}

	/**
	 * Show the form to edit a comment
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$comment = $this->api->show($id)->getOriginalData();

		// If the comment was not found or is not posted by the user
		if (is_null($comment) OR ! $comment->isPostedBySelf())
			return Redirect::home()->with('warning', Lang::get('comments.cantEditThisComment'));

		$data = compact('comment');
		$data['pageTitle'] = Lang::get('comments.updateCommentPageTitle');

		return View::make('comments.edit', $data);
	}

	/**
	 * Edit a comment
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$data = Input::only('content', 'quote_id');

		// Check if the form validates with success
		$validator = Validator::make($data, Comment::$rulesEdit);
		if ($validator->passes()) {

			// Call the API
			$response = $this->api->update($id);
			if ($response->getStatusCode() != 200)
				return Redirect::route('quotes.show', $data['quote_id'])->with('warning', Lang::get('comments.cantEditThisComment'));

			return Redirect::route('quotes.show', $data['quote_id'])->with('success', Lang::get('comments.commentEditSuccessfull'));
		}

		// Something went wrong
		return Redirect::route('comments.edit', $id)->withErrors($validator)->withInput(Input::all());
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