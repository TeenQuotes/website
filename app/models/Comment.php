<?php

class Comment extends Eloquent {
	protected $fillable = [];

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rules = [
		'content' => 'required|min:10|max:500',
		'user_id' => 'required|exists:users,id',
		'quote_id' => 'required|exists:quotes,id',
	];  

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function quote()
	{
		return $this->belongsTo('Quote');
	}
}