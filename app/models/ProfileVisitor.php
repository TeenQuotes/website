<?php

class ProfileVisitor extends Eloquent {
	protected $table = 'profile_visitors';
	protected $fillable = [];

	public function user()
	{
		return $this->belongsTo('User', 'id', 'user_id');
	}

	public function visitor()
	{
		return $this->belongsTo('User', 'id', 'visitor_id');
	}
}