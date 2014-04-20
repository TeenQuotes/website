<?php

class Story extends Eloquent {
	protected $table = 'stories';
	protected $fillable = [];

	/**
	 * The validation rules
	 * @var array
	 */
	public static $rules = [
		'represent_txt' => 'required|min:100|max:1000',
		'frequence_txt' => 'required|min:100|max:1000',
		'user_id' => 'required|exists:users,id',
	]; 

	public function user()
	{
		return $this->belongsTo('User', 'id', 'user_id');
	}
}