<?php namespace TeenQuotes\Tools\Composers;

use Config, Input, Lang;

abstract class AbstractDeepLinksComposer {

	protected function createDeepLinks($path)
	{
		$array = [];

		if (Config::get('mobile.iOSApp'))
			$array = $this->createForIos($array, $path);

		if (Config::get('mobile.androidApp'))
			$array = $this->createForAndroid($array, $path);

		return $array;
	}

	/**
	 * Create deep links data for iOS
	 *
	 * @param  array  $data The original data
	 * @param  string $path The current path
	 * @return array
	 */
	private function createForIos(array $data, $path)
	{
		$data['al:ios:url']          = $this->buildUrl($path);
		$data['al:ios:app_store_id'] = Config::get('mobile.iOSAppID');
		$data['al:ios:app_name']     = Lang::get('layout.nameWebsite');

		return $data;
	}

	/**
	 * Create deep links data for Android
	 *
	 * @param  array  $data The original data
	 * @param  string $path The current path
	 * @return array
	 */
	private function createForAndroid(array $data, $path)
	{
		$data['al:android:url']      = $this->buildUrl($path);
		$data['al:android:app_name'] = Lang::get('layout.nameWebsite');
		$data['al:android:package']  = Config::get('mobile.androidPackage');

		return $data;
	}

	/**
	 * Build a full URL
	 *
	 * @param  string $path The current path
	 * @return string
	 */
	private function buildUrl($path)
	{
		// If the content is paginated, update the path
		if (Input::has('page'))
			$path = $path.'?page='.Input::get('page');

		return Config::get('mobile.deepLinksProtocol').$path;
	}
}