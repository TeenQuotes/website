<?php

namespace TeenQuotes\Quotes\Composers;

use JavaScript;
use Lang;

class AddComposer
{
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
