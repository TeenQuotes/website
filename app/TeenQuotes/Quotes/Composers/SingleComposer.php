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
