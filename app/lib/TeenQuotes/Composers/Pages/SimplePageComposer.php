<?php
namespace TeenQuotes\Composers\Pages;

use Illuminate\Support\Facades\Route;
use TeenQuotes\Composers\AbstractDeepLinksComposer;

class SimplePageComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		// For deep links
		$view->with('deepLinksArray', $this->createDeepLinks(Route::currentRouteName()));
	}
}