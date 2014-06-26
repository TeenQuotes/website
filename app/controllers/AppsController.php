<?php

class AppsController extends BaseController {
	
	protected static $devicesInfo = [
		'tablet'  => [
			'name'       => 'Tablet',
			'mini-icon'  => 'fa-tablet',
			'large-icon' => 'tablet.png',
			'text-key'   => 'noAppYet', 
		],
		'ios'     => [
			'name'       => 'iOS',
			'mini-icon'  => 'fa-apple',
			'large-icon' => 'smartphone.png',
			'text-key'   => 'noAppYet', 
		],
		'android' => [
			'name'       => 'Android',
			'mini-icon'  => 'fa-android',
			'large-icon' => 'smartphone.png',
			'text-key'   => 'noAppYet', 
		],
		'mobile'  => [
			'name'       => 'Mobile',
			'mini-icon'  => 'fa-mobile',
			'large-icon' => 'smartphone.png',
			'text-key'   => 'noAppYet', 
		],
		'desktop' => [
			'name'       => 'Desktop',
			'mini-icon'  => 'fa-desktop',
			'large-icon' => 'computer.png',
			'text-key'   => 'noAppYet', 
		],
	];

	public function redirectPlural()
	{
		return Redirect::route('apps', null, 301);
	}

	public function index()
	{
		// Tablet
		if (Agent::isTablet())
			return Redirect::route('apps.device', 'tablet');
		
		// Mobile
		elseif (Agent::isMobile()) {
			if (Agent::isAndroidOS())
				return Redirect::route('apps.device', 'android');
			elseif (Agent::isiOS())
				return Redirect::route('apps.device', 'ios');
			
			return Redirect::route('apps.device', 'mobile');
		}

		// Desktop
		return Redirect::route('apps.device', 'desktop');
	}

	public function getDevice($device)
	{
		// Send event to Google Analytics
		JavaScript::put([
			'eventCategory' => 'apps',
			'eventAction'   => 'download-page',
			'eventLabel'    => Agent::platform().' - '.Agent::device()
    	]);

    	$data = [
			'title'           => Lang::get('apps.'.$device.'Title'),
			'titleIcon'       => $this->getIconTitle($device),
			'deviceType'      => $device,
			'content'         => Lang::get('apps.'.self::$devicesInfo[$device]['text-key'], ['url' => URL::route('contact')]),
			'devicesInfo'     => self::$devicesInfo,
			'pageTitle'       => Lang::get('apps.'.$device.'Title').' | '.Lang::get('layout.nameWebsite'),
			'pageDescription' => Lang::get('apps.pageDescription'),
    	];

    	return View::make('apps.download', $data);
	}

	private function getIconTitle($device) {
		switch ($device) {
			case 'android':
			case 'ios':
			case 'mobile':
				$result = 'fa-mobile';
				break;
			
			case 'tablet':
				$result = 'fa-tablet';
				break;
			case 'desktop':
				$result = 'fa-desktop';
				break;
		}

		return $result;
	}
}