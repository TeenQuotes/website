<?php namespace TeenQuotes\Comments\Repositories;

use InvalidArgumentException;
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
	 * Retrieve comments posted by a user for a page and a pagesize
	 * @param  TeenQuotes\Users\Models\User $user
	 * @param  int $page    
	 * @param  int $pagesize
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function findForUser(User $user, $page, $pagesize)
	{
		return $user->comments()
			->with('user', 'quote')
			->orderDescending()
			->take($pagesize)
			->skip($this->computeSkip($page, $pagesize))
			->get();
	}

	/**
	 * Post a comment on a quote
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
	 * Update the content of a comment
	 * @param  TeenQuotes\Comments\Models\Comment|int   $c
	 * @param  string $content
	 * @return TeenQuotes\Comments\Models\Comment
	 */
	public function update($c, $content)
	{
		$c = $this->retrieveComment($c);
		
		$c->content = $content;
		$c->save();
		
		return $c;
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

	/**
	 * Retrieve a comment by its ID or by its instance
	 * @param  TeenQuotes\Comments\Models\Comment|int $c
	 * @return TeenQuotes\Comments\Models\Comment
	 * @throws InvalidArgumentException If we can't retrieve a comment with the given data
	 */
	private function retrieveComment($c)
	{
		if (is_numeric($c))
			return $this->findById($c);
		
		if ($c instanceof Comment)
			return $c;

		throw new InvalidArgumentException("The given instance is not a comment");
	}

	private function computeSkip($page, $pagesize)
	{
		return $pagesize * ($page - 1);
	}
}