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

use Route;

class IndexForTagComposer extends IndexComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        // Bind to the view the name of the tag
        $view->with('tagName', Route::input('tag_name'));

        // Delegate the difficult stuff to the parent
        parent::compose($view);
    }
}
