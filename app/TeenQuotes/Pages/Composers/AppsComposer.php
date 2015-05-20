<?php

namespace TeenQuotes\Pages\Composers;

use Agent;
use JavaScript;

class AppsComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        // Data for Google Analytics
        JavaScript::put([
            'eventCategory' => 'apps',
            'eventAction'   => 'download-page',
            'eventLabel'    => Agent::platform().' - '.Agent::device(),
        ]);
    }
}
