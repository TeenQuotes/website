<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Composers;

use JavaScript;
use Lang;

class AddComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        JavaScript::put([
            'contentShortHint' => Lang::get('quotes.contentShortHint'),
            'contentGreatHint' => Lang::get('quotes.contentGreatHint'),
            'eventCategory'    => 'addquote',
            'eventAction'      => 'logged-in',
            'eventLabel'       => 'addquote-page',
        ]);
    }
}
