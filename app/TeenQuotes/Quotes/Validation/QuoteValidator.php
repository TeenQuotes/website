<?php namespace TeenQuotes\Quotes\Validation;

use TeenQuotes\Tools\Validation\Validator as BaseValidator;

class QuoteValidator extends BaseValidator {
	
	/**
	 * The validation rules when posting a quote
	 * @var array
	 */
	protected $rulesPosting = [
		'content'              => 'required|min:50|max:300|unique:quotes,content',
		'quotesSubmittedToday' => 'required|integer|between:0,4',
	];

	protected $rulesNbSubmittedToday = [
		'quotesSubmittedToday' => 'required|integer|between:0,4',
	];
}