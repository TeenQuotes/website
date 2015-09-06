<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tools\Composers;

use Route;

class DeepLinksComposer extends AbstractDeepLinksComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        // For deep links
        $view->with('deepLinksArray', $this->createDeepLinks(Route::currentRouteName()));
    }
}
