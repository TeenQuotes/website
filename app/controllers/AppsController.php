<?php

class AppsController extends BaseController {
	
	public function index()
	{
		// Tablet
		if (Agent::isTablet())
			return Redirect::route('apps.tablet');
		// Mobile
		elseif (Agent::isMobile()) {
			if (Agent::isAndroidOS())
				return Redirect::route('apps.android');
			elseif (Agent::isiOS())
				return Redirect::route('apps.ios');
			
			return Redirect::route('apps.mobile');
		}

		// Desktop
		return Redirect::route('apps.desktop');
	}

	public function getDevice($device)
	{
		return $device;
	}
}