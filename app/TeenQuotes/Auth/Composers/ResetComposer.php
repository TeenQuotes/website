<?php namespace TeenQuotes\Auth\Composers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;

class ResetComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		$data = $view->getData();
		$token = $data['token'];

		// For deep links
		$view->with('deepLinksArray', $this->createDeepLinks('password/reset?token='.$token));
	}
}