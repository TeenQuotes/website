<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $data  = $view->getData();
        $token = $data['token'];

        // For deep links
        $view->with('deepLinksArray', $this->createDeepLinks('password/reset?token='.$token));
    }
}
