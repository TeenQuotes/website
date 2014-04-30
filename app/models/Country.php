<?php

class Country extends \Eloquent {
	protected $table = 'countries';
	public $timestamps = false;
	protected $fillable = ['name'];
}