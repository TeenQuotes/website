<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
