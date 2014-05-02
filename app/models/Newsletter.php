<?php

class Newsletter extends Eloquent {
	protected $fillable = [];

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function scopeType($query, $type)
	{
		if (!in_array($type, array('weekly', 'daily')))
    		throw new InvalidArgumentException("Newsletter's type only accepts weekly or daily. ".$type." was given.");

    	return $query->whereType($type);
	}

	public function scopeForUser($query, User $user)
	{
		return $query->where('user_id', '=', $user->id);
	}
}