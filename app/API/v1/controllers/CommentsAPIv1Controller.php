<?php

class CommentsAPIv1Controller extends BaseController {

	public function index()
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.comments.nbCommentsPerPage'));

        if ($page <= 0)
			$page = 1;

		// Number of comments to skip
        $skip = $pagesize * ($page - 1);

		$totalComments = Comment::count();

        // Get comments
        $contentQuery = Comment::
			withSmallUser()
			->orderDescending();

		if (Input::has('quote'))
			$contentQuery = $contentQuery->with('quote');

		$content = $contentQuery
			->take($pagesize)
			->skip($skip)
			->get();

		// Handle no comments found
		if (is_null($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No comments have been found.'
			];

			return Response::json($data, 404);
		}

		$data = APIGlobalController::paginateContent($page, $pagesize, $totalComments, $content, 'comments');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function show($comment_id)
	{
		$commentQuery = Comment::where('id', '=', $comment_id)
			->withSmallUser();
		
		if (Input::has('quote'))
			$commentQuery = $commentQuery->with('quote');
		
		$comment = $commentQuery->first();

		// Handle not found
		if (is_null($comment)) {

			$data = [
				'status' => 'comment_not_found',
				'error'  => "The comment #".$comment_id." was not found",
			];

			return Response::json($data, 404);
		}
		else
			return Response::json($comment, 200, [], JSON_NUMERIC_CHECK);
	}

	public function store($quote_id, $doValidation = true)
	{
		$user = ResourceServer::getOwnerId() ? User::find(ResourceServer::getOwnerId()) : Auth::user();
		$content = Input::get('content');

		if ($doValidation) {		
			
			// Validate quote_id
			$validatorQuote = Validator::make(compact('quote_id'), ['quote_id' => Comment::$rulesAdd['quote_id']]);
			if ($validatorQuote->fails()) {
				$data = [
					'status' => 'wrong_quote_id',
					'error' => $validatorQuote->messages()->first('quote_id')
				];

				return Response::json($data, 400);
			}

			// Validate content
			$validatorContent = Validator::make(compact('content'), ['content' => Comment::$rulesAdd['content']]);
			if ($validatorContent->fails()) {
				$data = [
					'status' => 'wrong_content',
					'error' => $validatorContent->messages()->first('content')
				];

				return Response::json($data, 400);
			}
		}

		$quote = Quote::where('id', '=', $quote_id)->with('user')->first();
		
		// Check if the quote is published
		if (!$quote->isPublished()) {
			$data = [
				'status' => 'wrong_quote_id',
				'error' => 'The quote should be published.'
			];

			return Response::json($data, 400);
		}

		// Store the comment
		$comment = new Comment;
		$comment->content  = $content;
		$comment->quote_id = $quote_id;
		$comment->user_id  = $user->id;
		$comment->save();

		// Send an email to the author of the quote if he wants it
		if ($quote->user->wantsEmailComment()) {
			$emailData = array();
			$emailData['quote']   = $quote->toArray();
			$emailData['comment'] = $comment->toArray();

			// Send the email via SMTP
			new MailSwitcher('smtp');
			Mail::send('emails.comments.posted', $emailData, function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('comments.commentAddedSubjectEmail', ['id' => $quote->id]));
			});
		}

		// If we have the number of comments in cache, increment it
		if (Cache::has(Quote::$cacheNameNbComments.$quote_id))
			Cache::increment(Quote::$cacheNameNbComments.$quote_id);

		return Response::json($comment, 200, [], JSON_NUMERIC_CHECK);
	}
}