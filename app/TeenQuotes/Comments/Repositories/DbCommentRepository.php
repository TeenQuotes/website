<?php namespace TeenQuotes\Comments\Repositories;

use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;

class DbCommentRepository implements CommentRepository {

	/**
	 * Retrieve a comment thanks to its ID
	 * @param  int $id
	 * @return TeenQuotes\Comments\Models\Comment
	 */
	public function findById($id)
	{
		return Comment::where('id', '=', $id)
			->withSmallUser()
			->first();
	}

	/**
	 * Retrieve a comment thanks to its ID and add the related quote
	 * @param  int $id 
	 * @return TeenQuotes\Comments\Models\Comment
	 */
	public function findByIdWithQuote($id)
	{
		return Comment::where('id', '=', $id)
			->withSmallUser()
			->with('quote')
			->first();
	}

	/**
	 * List quotes for a given quote, page and pagesize
	 * @param  int $quote_id 
	 * @param  int $page     
	 * @param  int $pagesize
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function indexForQuote($quote_id, $page, $pagesize)
	{
		return Comment::forQuoteId($quote_id)
			->withSmallUser()
			->orderDescending()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * List quotes for a given quote, page and pagesize and add the related quotes
	 * @param  int $quote_id 
	 * @param  int $page     
	 * @param  int $pagesize
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function indexForQuoteWithQuote($quote_id, $page, $pagesize)
	{
		return Comment::forQuoteId($quote_id)
			->withSmallUser()
			->with('quote')
			->orderDescending()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Create a quote
	 * @param  TeenQuotes\Quotes\Models\Quote  $q
	 * @param  TeenQuotes\Users\Models\User   $u
	 * @param  string $content
	 * @return TeenQuotes\Comments\Models\Comment
	 */
	public function create(Quote $q, User $u, $content)
	{
		$comment = new Comment;
		$comment->content  = $content;
		$comment->quote_id = $q->id;
		$comment->user_id  = $u->id;
		$comment->save();
		
		return $comment;
	}

	/**
	 * Delete a comment
	 * @param  int $id 
	 * @return TeenQuotes\Comments\Models\Comment
	 */
	public function delete($id)
	{
		return Comment::find($id)->delete();
	}

	private function computeSkip($page, $pagesize)
	{
		return $pagesize * ($page - 1);
	}
}