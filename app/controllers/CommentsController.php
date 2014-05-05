<?php

class CommentsController extends \BaseController {

	public function __construct()
	{
		$this->beforeFilter('auth', array('on' => 'store'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		$data = [
			'content'  => Input::get('content'),
			'quote_id' => Input::get('quote_id'),
		];

		$validator = Validator::make($data, Comment::$rulesAdd);
		$quote = Quote::where('id', '=', $data['quote_id'])->with('user')->first();

		// Check if the form validates with success.
		if ($validator->passes() AND !is_null($quote) AND $quote->isPublished()) {

			// Store the comment
			$comment = new Comment;
			$comment->content  = $data['content'];
			$comment->quote_id = $data['quote_id'];
			$comment->user_id  = Auth::user()->id;
			$comment->save();

			// Send an email to the author of the quote if he wants it
			if ($quote->user->wantsEmailComment()) {
				$emailData = array();
				$emailData['quote']   = $quote->toArray();
				$emailData['comment'] = $comment->toArray();

				Mail::send('emails.comments.posted', $emailData, function($m) use($quote)
				{
					$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('comments.commentAddedSubjectEmail', ['id' => $quote->id]));
				});
			}

			// If we have the number of comments in cache, increment it
			if (Cache::has(Quote::$cacheNameNbComments.$data['quote_id']))
				Cache::increment(Quote::$cacheNameNbComments.$data['quote_id']);

			return Redirect::action('QuotesController@show',  ['id' => $data['quote_id']])->with('success', Lang::get('comments.commentAddedSuccessfull'));
		}

		// Something went wrong.
		return Redirect::action('QuotesController@show',  ['id' => $data['quote_id']])->withErrors($validator)->withInput(Input::all());
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