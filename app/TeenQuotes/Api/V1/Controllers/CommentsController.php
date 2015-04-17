<?php namespace TeenQuotes\Api\V1\Controllers;

use App, Config, Input;
use TeenQuotes\Api\V1\Interfaces\PaginatedContentInterface;
use TeenQuotes\Exceptions\ApiNotFoundException;
use TeenQuotes\Http\Facades\Response;

class CommentsController extends APIGlobalController implements PaginatedContentInterface {

	/**
	 * @var \TeenQuotes\Comments\Validation\CommentValidator
	 */
	private $commentValidator;

	public function bootstrap()
	{
		$this->commentValidator = App::make('TeenQuotes\Comments\Validation\CommentValidator');
	}

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
		if ($this->isNotFound($content))
			throw new ApiNotFoundException('comments');

		// Get the total number of comments for the related quote
		$relatedQuote = $this->quoteRepo->getById($quote_id);
		$totalComments = $relatedQuote->total_comments;

		$data = $this->paginateContent($page, $pagesize, $totalComments, $content, 'comments');

		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function getCommentsForUser($user_id)
	{
		$page = $this->getPage();
		$pagesize = $this->getPagesize();

		$user = $this->userRepo->getById($user_id);

		// Handle user not found
		if (is_null($user))
			return $this->tellUserWasNotFound($user_id);

		// Get comments
		$content = $this->commentRepo->findForUser($user, $page, $pagesize);

		// Handle no comments found
		$totalComments = 0;
		if ($this->isNotFound($content))
			throw new ApiNotFoundException('comments');

		$totalComments = $this->commentRepo->countForUser($user);

		$data = $this->paginateContent($page, $pagesize, $totalComments, $content, 'comments');

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

		if ($doValidation)
			$this->commentValidator->validatePosting(compact('content', 'quote_id'));

		$quote = $this->quoteRepo->getById($quote_id);

		// Check if the quote is published
		if ( ! $quote->isPublished())
			return Response::json([
				'status' => 'wrong_quote_id',
				'error' => 'The quote should be published.'
			], 400);

		// Store the comment
		$comment = $this->commentRepo->create($quote, $user, $content);

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
			return $this->tellCommentWasNotPostedByUser($id, $user->id);

		// Perform validation
		$this->commentValidator->validateEditing(compact('content'));

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
			return $this->tellCommentWasNotPostedByUser($id, $user->id);

		// Delete the comment
		$this->commentRepo->delete($id);

		return Response::json([
			'status'  => 'comment_deleted',
			'success' => "The comment #".$id." was deleted.",
		], 200);
	}

	/**
	 * Get the pagesize
	 *
	 * @return int
	 */
	public function getPagesize()
	{
		return Input::get('pagesize', Config::get('app.comments.nbCommentsPerPage'));
	}

	/**
	 * Tell that the comment was not found
	 *
	 * @param  int $id
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function tellCommentWasNotFound($id)
	{
		return Response::json([
			'status' => 'comment_not_found',
			'error'  => "The comment #".$id." was not found.",
		], 404);
	}

	/**
	 * Tell that a comment a comment was not posted by a user
	 *
	 * @param  int $id The ID of the comment
	 * @param  int $user_id The ID of the user
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function tellCommentWasNotPostedByUser($id, $user_id)
	{
		return Response::json([
			'status' => 'comment_not_self',
			'error'  => "The comment #".$id." was not posted by user #".$user_id.".",
		], 400);
	}

	/**
	 * Tell that we can't find a user
	 *
	 * @param  int $user_id
	 * @return \TeenQuotes\Http\Facades\Response
	 */
	private function tellUserWasNotFound($user_id)
	{
		return Response::json([
			'status' => 'user_not_found',
			'error'  => "The user #".$user_id." was not found.",
		], 400);
	}
}
