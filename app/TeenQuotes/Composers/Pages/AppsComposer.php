<?php namespace TeenQuotes\Composers\Pages;

use Agent;
use JavaScript;

class AppsComposer {

	public function compose($view)
	{
		// Data for Google Analytics
		JavaScript::put([
			'eventCategory' => 'apps',
			'eventAction'   => 'download-page',
			'eventLabel'    => Agent::platform().' - '.Agent::device()
		]);
	}
}