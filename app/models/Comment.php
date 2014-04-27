<?php

class Comment extends Eloquent {
	protected $fillable = [];

	/**
	 * The validation rules when adding a comment
	 * @var array
	 */
	public static $rulesAdd = [
		'content' => 'required|min:10|max:500',
		'quote_id' => 'required|exists:quotes,id',
	];

	public static $cacheNameQuotesPage = 'quotes_homepage_';


	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}
}