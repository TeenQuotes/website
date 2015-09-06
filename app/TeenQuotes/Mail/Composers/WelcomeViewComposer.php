<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $login    = $viewData['login'];

        // Construct a URL to track with Google Analytics
        $urlProfile         = URL::route('users.show', $login);
        $urlCampaignProfile = TextTools::linkCampaign($urlProfile, 'callToProfile', 'email', 'welcome', 'linkBodyEmail');

        $data = compact('login', 'urlCampaignProfile', 'urlProfile');

        // Content
        $view->with('data', $data);
    }
}
