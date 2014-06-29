<?php
namespace TeenQuotes\Composers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

abstract class AbstractDeepLinksComposer {

	protected function createDeepLinks($path)
	{
		$array = [
			'al:ios:url'          => Config::get('mobile.deepLinksProtocol').$path,
			'al:ios:app_store_id' => Config::get('mobile.iOSAppID'),
			'al:ios:app_name'     => Lang::get('layout.nameWebsite')
		];

		return $array;
	}
}