<?php namespace TeenQuotes\Settings\Models\Relations;

trait SettingTrait {
	
	public function user()
	{
		return $this->belongsTo('TeenQuotes\Users\Models\User', 'user_id', 'id');
	}
}