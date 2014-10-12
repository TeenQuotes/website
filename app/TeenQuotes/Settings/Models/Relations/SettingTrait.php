<?php namespace TeenQuotes\Settings\Models\Relations;

trait SettingTrait {
	
	public function user()
	{
		return $this->belongsTo('User', 'user_id', 'id');
	}
}