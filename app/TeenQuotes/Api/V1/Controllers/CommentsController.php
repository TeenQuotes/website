<?php namespace TeenQuotes\Api\V1\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;

class CommentsController extends APIGlobalController implements PaginatedContentInterface {

	public function index($quote_id)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();

		// Get comments
		if (Input::has('quote'))
			$content = $this->commentRepo->indexForQuoteWithQuote($quote_id, $page, $pagesize);
		else
			$content = $this->commentRepo->indexForQuote($quote_id, $page, $pagesize);

		// Handle no comments found
		if (is_null($content) OR $content->count() == 0)
			throw new ApiNotFoundException('comments');
		
		// Get the total number of comments for the related quote
		$relatedQuote = $this->quoteRepo->getById($quote_id);
		$totalComments = $relatedQuote->total_comments;

		$data = self::paginateContent($page, $pagesize, $totalComments, $content, 'comments');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function show($comment_id)
	{		
		if (Input::has('quote'))
			$comment = $this->commentRepo->findByIdWithQuote($comment_id);
		else
			$comment = $this->commentRepo->findById($comment_id);
		
		// Handle not found
		if (is_null($comment))
			return $this->tellCommentWasNotFound($comment_id);
		
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

		$quote = $this->quoteRepo->getById($quote_id);
		
		// Check if the quote is published
		if ( ! $quote->isPublished())
			return Response::json([
				'status' => 'wrong_quote_id',
				'error' => 'The quote should be published.'
			], 400);

		// Store the comment
		$comment = $this->commentRepo->create($quote, $user, $content);

		// Send an e-mail to the author of the quote if he wants it
		// Update number of comments in cache

		return Response::json($comment, 201, [], JSON_NUMERIC_CHECK);
	}

	public function update($id)
	{
		$user = $this->retrieveUser();
		$content = Input::get('content');
		$comment = $this->commentRepo->findById($id);

		// Handle not found
		if (is_null($comment))
			return $this->tellCommentWasNotFound($id);

		// Check that the user is the owner of the comment
		if ( ! $comment->isPostedByUser($user))
			return $this->tellCommentWasNotPostedByUser($id, $user);

		// Perform validation
		foreach (array_keys(Comment::$rulesEdit) as $value) {
			$validator = Validator::make(compact($value), [$value => Comment::$rulesEdit[$value]]);
			if ($validator->fails())
				return Response::json([
					'status' => 'wrong_'.$value,
					'error' => $validator->messages()->first($value)
				], 400);
		}

		// Update the comment
		$this->commentRepo->update($id, $content);

		return Response::json([
			'status'  => 'comment_updated',
			'success' => "The comment #".$id." was updated.",
		], 200);
	}

	public function destroy($id)
	{
		$user = $this->retrieveUser();
		$comment = $this->commentRepo->findById($id);

		// Handle not found
		if (is_null($comment))
			return $this->tellCommentWasNotFound($id);

		// Check that the user is the owner of the comment
		if ( ! $comment->isPostedByUser($user))
			return $this->tellCommentWasNotPostedByUser($id, $user);

		// Delete the comment
		$this->commentRepo->delete($id);
		
		// Decrease the number of comments on the quote in cache

		return Response::json([
			'status'  => 'comment_deleted',
			'success' => "The comment #".$id." was deleted.",
		], 200);
	}

	public function getPagesize()
	{
		return Input::get('pagesize', Config::get('app.comments.nbCommentsPerPage'));
	}

	private function tellCommentWasNotFound($id)
	{
		return Response::json([
			'status' => 'comment_not_found',
			'error'  => "The comment #".$id." was not found.",
		], 404);
	}

	private function tellCommentWasNotPostedByUser($id, User $user)
	{
		return Response::json([
			'status' => 'comment_not_self',
			'error'  => "The comment #".$id." was not posted by user #".$user->id.".",
		], 400);
	}
}