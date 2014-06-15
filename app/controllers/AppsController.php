<?php

class AppsController extends BaseController {
	
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

    	return View::make('apps.download', ['content' => $device]);
	}
}