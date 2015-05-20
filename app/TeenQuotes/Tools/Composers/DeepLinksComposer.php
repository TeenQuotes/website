<?php

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
