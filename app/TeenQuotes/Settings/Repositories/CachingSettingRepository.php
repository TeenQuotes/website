<?php namespace TeenQuotes\Settings\Repositories;

use TeenQuotes\Users\Models\User;
use Illuminate\Cache\Repository as Cache;

class CachingSettingRepository implements SettingRepository {

	/**
	 * @var \TeenQuotes\Settings\Repositories\SettingRepository
	 */
	private $settings;

	/**
	 * @var \Illuminate\Cache\Repository
	 */
	private $cache;

	public function __construct(SettingRepository $settings, Cache $cache)
	{
		$this->settings = $settings;
		$this->cache    = $cache;
	}

	/**
	 * Update or create a setting for a given user and key
	 *
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @param  string $key
	 * @param  mixed $value
	 * @return \TeenQuotes\Settings\Models\Setting
	 */
	public function updateOrCreate(User $u, $key, $value)
	{
		// If the value was stored in cache, delete it
		$cacheKeyName = $this->constructCacheName($u, $key);
		if ($this->cache->has($cacheKeyName))
			$this->cache->forget($cacheKeyName);

		return $this->settings->updateOrCreate($u, $key, $value);
	}

	/**
	 * Retrieve a row for a given user and key
	 *
	 * @param  \TeenQuotes\Users\Models\User $u
	 * @param  string $key
	 * @return \TeenQuotes\Settings\Models\Setting
	 */
	public function findForUserAndKey(User $u, $key)
	{
		$settingRepo = $this->settings;

		// Get the value from cache or touch the database
		return $this->cache->remember($this->constructCacheName($u, $key), 10, function() use ($settingRepo, $u, $key)
		{
			return $settingRepo->findForUserAndKey($u, $key);
		});
	}

	private function constructCacheName(User $u, $key)
	{
		return 'settings.user-'.$u->id.'.'.$key;
	}
}