<?php

class Country extends \Eloquent {
	protected $table = 'countries';
	public $timestamps = false;
	protected $fillable = ['name'];

	public static $idUSA = 224;

	public function users()
	{
		return $this->hasMany('User', 'country', 'id');
	}
}