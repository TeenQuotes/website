<?php

class Setting extends Eloquent {
	protected $fillable = ['user_id', 'key', 'value'];
	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User', 'id', 'user_id');
	}
}