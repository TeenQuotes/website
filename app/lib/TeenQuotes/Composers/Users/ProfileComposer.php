<?php
namespace TeenQuotes\Composers\Users;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use TeenQuotes\Composers\AbstractDeepLinksComposer;

class ProfileComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		$viewData = $view->getData();
		$login = $viewData['user']->login;
		$showType = $viewData['showType'];

		$welcomeText = Lang::get('users.newUserWelcomeProfile', ['login' => $login]);
		
		$updateProfileTitle = Lang::get('users.newUserTutorialProfileTitle');
		$updateProfileContent = Lang::get('users.newUserTutorialProfileContent', ['url' => URL::route('users.edit', $login)]);
		
		$addingQuoteTitle = Lang::get('users.newUserTutorialAddingQuoteTitle');
		$addingQuoteContent = Lang::get('users.newUserTutorialAddingQuoteContent', ['url' => URL::route('addquote')]);
		
		$addingFavoritesTitle = Lang::get('users.newUserTutorialFavoritesTitle');
		$addingFavoritesContent = Lang::get('users.newUserTutorialFavoritesContent');
		
		// Content
		$view->with('welcomeText', $welcomeText);
		$view->with('updateProfileTitle', $updateProfileTitle);
		$view->with('updateProfileContent', $updateProfileContent);
		$view->with('addingQuoteTitle', $addingQuoteTitle);
		$view->with('addingQuoteContent', $addingQuoteContent);
		$view->with('addingFavoritesTitle', $addingFavoritesTitle);
		$view->with('addingFavoritesContent', $addingFavoritesContent);

		// For deep links
		$view->with('deepLinksArray', $this->createDeepLinks('users/'.$login.'/'.$showType));
	}
}