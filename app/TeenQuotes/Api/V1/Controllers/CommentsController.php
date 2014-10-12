<?php namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Mail\MailSwitcher;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;

class CommentsController extends APIGlobalController implements PaginatedContentInterface {

	public function index($quote_id)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();
		
		// Number of comments to skip
		$skip = $pagesize * ($page - 1);

		// Get comments
		$contentQuery = Comment::forQuoteId($quote_id)
			->withSmallUser()
			->orderDescending();

		if (Input::has('quote'))
			$contentQuery = $contentQuery->with('quote');

		$content = $contentQuery->take($pagesize)
			->skip($skip)
			->get();

		// Handle no comments found
		if (is_null($content) OR $content->count() == 0)
			return Response::json([
				'status' => 404,
				'error' => 'No comments have been found.'
			], 404);
		
		// Get the total number of comments for the related quote
		$relatedQuote = Quote::find($quote_id);
		$totalComments = $relatedQuote->total_comments;

		$data = self::paginateContent($page, $pagesize, $totalComments, $content, 'comments');
		
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
		if (is_null($comment))
			return Response::json([
				'status' => 'comment_not_found',
				'error'  => "The comment #".$comment_id." was not found",
			], 404);
		
		return Response::json($comment, 200, [], JSON_NUMERIC_CHECK);
	}

	public function store($quote_id, $doValidation = true)
	{
		$user = $this->retrieveUser();
		$content = Input::get('content');

		if ($doValidation) {

			foreach (array_keys(Comment::$rulesAdd) as $value) {
				$validator = Validator::make(compact($value), [$value => Comment::$rulesAdd[$value]]);
				if ($validator->fails())
					return Response::json([
						'status' => 'wrong_'.$value,
						'error' => $validator->messages()->first($value)
					], 400);
			}
		}

		$quote = Quote::find($quote_id);
		
		// Check if the quote is published
		if ( ! $quote->isPublished())
			return Response::json([
				'status' => 'wrong_quote_id',
				'error' => 'The quote should be published.'
			], 400);

		// Store the comment
		$comment = new Comment;
		$comment->content  = $content;
		$comment->quote_id = $quote_id;
		$comment->user_id  = $user->id;
		$comment->save();

		// Send an e-mail to the author of the quote if he wants it
		// Update number of comments in cache

		return Response::json($comment, 201, [], JSON_NUMERIC_CHECK);
	}

	public function getPagesize()
	{
		return Input::get('pagesize', Config::get('app.comments.nbCommentsPerPage'));
	}

	public function destroy($id)
	{
		$user = $this->retrieveUser();
		$comment = Comment::find($id);

		// Handle not found
		if (is_null($comment))
			return Response::json([
				'status' => 'comment_not_found',
				'error'  => "The comment #".$id." was not found.",
			], 404);

		// Check that the user is the owner of the comment
		if ( ! $comment->isPostedByUser($user))
			return Response::json([
				'status' => 'comment_not_self',
				'error'  => "The comment #".$id." was not posted by user #".$user->id.".",
			], 400);

		// Delete the comment
		$comment->delete();
		
		// Decrease the number of comments on the quote in cache

		return Response::json([
			'status'  => 'comment_deleted',
			'success' => "The comment #".$id." was deleted.",
		], 200);
	}
}