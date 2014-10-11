<?php namespace TeenQuotes\Models\Observers;

use Illuminate\Support\Facades\Cache;
use User;

class SettingObserver {
	
	/**
	 * Will be triggered when a model is created
	 * @param Setting $setting
	 */
	public function saved($setting)
	{
		if ($setting->key == 'colorsQuotesPublished')
			$this->handleSavedForColorsQuotesPublished($setting);
	}

	private function handleSavedForColorsQuotesPublished($setting)
	{
		// Forget value in cache
		Cache::forget(User::$cacheNameForColorsQuotesPublished.$setting->user_id);
	}
}