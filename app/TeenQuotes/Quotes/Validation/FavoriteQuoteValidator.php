<?php namespace TeenQuotes\Quotes\Validation;

use TeenQuotes\Tools\Validation\Validator as BaseValidator;
use TeenQuotes\Users\Models\User;

class FavoriteQuoteValidator extends BaseValidator {

	/**
	 * The validation rules when adding a favorite quote
	 * @var array
	 */
	protected $rulesPost = [
		'quote_id' => 'required|exists:quotes,id',
		'user_id'  => 'required|exists:users,id',
	];

	/**
	 * @var array
	 */
	protected $rulesPostForQuote = [
		'quote_id' => 'required|exists:quotes,id',
	];

	/**
	 * The validation rules when deleting a favorite quote
	 * @var array
	 */
	protected $rulesRemove = [
		'quote_id' => 'required|exists:quotes,id|exists:favorite_quotes,quote_id',
		'user_id'  => 'required|exists:users,id|exists:favorite_quotes,user_id',
	];

	protected $rulesRemoveForQuote = [
		'quote_id' => 'exists:favorite_quotes,quote_id,user_id,'
	];

	protected function setUserForRemove(User $u)
	{
		$this->rulesRemoveForQuote['quote_id'] = $this->rulesRemoveForQuote['quote_id'].$u->id;

		return $this;
	}
}