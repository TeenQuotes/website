<?php

class ProfileComposer {

    public function compose($view)
    {
        $viewData = $view->getData();
        $login = $viewData['user']->login;

        $welcomeText = Lang::get('users.newUserWelcomeProfile', ['login' => $login]);
        
        $updateProfileTitle = Lang::get('users.newUserTutorialProfileTitle');
        $updateProfileContent = Lang::get('users.newUserTutorialProfileContent', ['url' => URL::route('users.edit', $login)]);
        
        $addingQuoteTitle = Lang::get('users.newUserTutorialAddingQuoteTitle');
        $addingQuoteContent = Lang::get('users.newUserTutorialAddingQuoteContent', ['url' => URL::route('addquote')]);
        
        $addingFavoritesTitle = Lang::get('users.newUserTutorialFavoritesTitle');
        $addingFavoritesContent = Lang::get('users.newUserTutorialFavoritesContent');
        
        $view->with('welcomeText', $welcomeText);
        $view->with('updateProfileTitle', $updateProfileTitle);
        $view->with('updateProfileContent', $updateProfileContent);
        $view->with('addingQuoteTitle', $addingQuoteTitle);
        $view->with('addingQuoteContent', $addingQuoteContent);
        $view->with('addingFavoritesTitle', $addingFavoritesTitle);
        $view->with('addingFavoritesContent', $addingFavoritesContent);
    }
}