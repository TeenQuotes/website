<?php

namespace TeenQuotes\Quotes\Composers;

use JavaScript;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;
use URL;

class SingleComposer extends AbstractDeepLinksComposer
{
    public function compose($view)
    {
        JavaScript::put([
            'urlFavoritesInfo' => URL::route('quotes.favoritesInfo'),
        ]);
    }
}
