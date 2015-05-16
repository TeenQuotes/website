<?php

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
