<?php

namespace TeenQuotes\Auth\Composers;

use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;

class ResetComposer extends AbstractDeepLinksComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = $view->getData();
        $token = $data['token'];

        // For deep links
        $view->with('deepLinksArray', $this->createDeepLinks('password/reset?token='.$token));
    }
}
