<?php

namespace TeenQuotes\Tools\Composers;

use Route;

class DeepLinksComposer extends AbstractDeepLinksComposer
{
    public function compose($view)
    {
        // For deep links
        $view->with('deepLinksArray', $this->createDeepLinks(Route::currentRouteName()));
    }
}
