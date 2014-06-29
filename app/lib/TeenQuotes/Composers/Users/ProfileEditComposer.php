<?php
namespace TeenQuotes\Composers\Users;

use TeenQuotes\Composers\AbstractDeepLinksComposer;

class ProfileEditComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		$viewData = $view->getData();
		$login = $viewData['user']->login;

		// For deep links
		$view->with('deepLinksArray', $this->createDeepLinks('users/'.$login.'/edit'));
	}
}