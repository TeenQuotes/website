<?php namespace TeenQuotes\Composers\Users;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use TeenQuotes\Composers\AbstractDeepLinksComposer;

class WelcomeComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		$viewData = $view->getData();
		$login = $viewData['user']->login;
		$type = $viewData['type'];

		$welcomeText = Lang::get('users.newUserWelcomeProfile', ['login' => $login]);
		
		$updateProfileTitle = Lang::get('users.newUserTutorialProfileTitle');
		$updateProfileContent = Lang::get('users.newUserTutorialProfileContent', ['url' => URL::route('users.edit', $login)]);
		
		$addingQuoteTitle = Lang::get('users.newUserTutorialAddingQuoteTitle');
		$addingQuoteContent = Lang::get('users.newUserTutorialAddingQuoteContent', ['url' => URL::route('addquote')]);
		
		$addingFavoritesTitle = Lang::get('users.newUserTutorialFavoritesTitle');
		$addingFavoritesContent = Lang::get('users.newUserTutorialFavoritesContent');

		$editSettingsTitle = Lang::get('users.newUserTutorialSettingsTitle');
		$editSettingsContent = Lang::get('users.newUserTutorialSettingsContent', ['url' => URL::route('users.edit', $login)."#edit-settings"]);

		// Content
		$view->with('welcomeText', $welcomeText);
		$view->with('updateProfileTitle', $updateProfileTitle);
		$view->with('updateProfileContent', $updateProfileContent);
		$view->with('addingQuoteTitle', $addingQuoteTitle);
		$view->with('addingQuoteContent', $addingQuoteContent);
		$view->with('addingFavoritesTitle', $addingFavoritesTitle);
		$view->with('addingFavoritesContent', $addingFavoritesContent);
		$view->with('editSettingsTitle', $editSettingsTitle);
		$view->with('editSettingsContent', $editSettingsContent);

		// For deep links
		$view->with('deepLinksArray', $this->createDeepLinks('users/'.$login.'/'.$type));
	}
}