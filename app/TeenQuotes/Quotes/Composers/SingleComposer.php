<?php

namespace TeenQuotes\Quotes\Composers;

use JavaScript;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;
use URL;

class SingleComposer extends AbstractDeepLinksComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        JavaScript::put([
            'urlFavoritesInfo' => URL::route('quotes.favoritesInfo'),
        ]);
    }
}
