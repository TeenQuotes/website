<?php

class AppsController extends BaseController {
	
	public static $devicesInfo = [
		'tablet'  => ['name' => 'Tablet', 'icon' => 'fa-tablet'],
		'ios'     => ['name' => 'iOS', 'icon' => 'fa-apple'],
		'android' => ['name' => 'Android', 'icon' => 'fa-android'],
		'mobile'  => ['name' => 'Mobile', 'icon' => 'fa-mobile'],
		'desktop' => ['name' => 'Desktop', 'icon' => 'fa-desktop'],
	];

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
			'content'         => $device,
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
			case 'desktop':
				$result = 'fa-desktop';
				break;
		}

		return $result;
	}
}