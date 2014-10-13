<?php namespace TeenQuotes\Tools\Composers;

use Illuminate\Support\Facades\Route;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;

class DeepLinksComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		// For deep links
		$view->with('deepLinksArray', $this->createDeepLinks(Route::currentRouteName()));
	}
}