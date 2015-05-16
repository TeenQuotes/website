<?php

namespace TeenQuotes\Mail\Composers;

use TextTools;
use URL;

class WelcomeViewComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $viewData = $view->getData();
        $login = $viewData['login'];

        // Construct a URL to track with Google Analytics
        $urlProfile = URL::route('users.show', $login);
        $urlCampaignProfile = TextTools::linkCampaign($urlProfile, 'callToProfile', 'email', 'welcome', 'linkBodyEmail');

        $data = [
            'login'              => $login,
            'urlCampaignProfile' => $urlCampaignProfile,
            'urlProfile'         => $urlProfile,
        ];

        // Content
        $view->with('data', $data);
    }
}
