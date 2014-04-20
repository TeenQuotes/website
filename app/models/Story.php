<?php

class Story extends Eloquent {
	protected $table = 'stories';
	protected $fillable = [];

	public function user()
	{
		return $this->belongsTo('User', 'id', 'user_id');
	}
}