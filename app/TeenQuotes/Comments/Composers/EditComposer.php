<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Comments\Composers;

use JavaScript;
use Lang;

class EditComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        // Put some useful variables for the JS
        JavaScript::put([
            'contentShortHisnt' => Lang::get('comments.contentShortHint'),
            'contentGreatHint'  => Lang::get('comments.contentGreatHint'),
        ]);
    }
}
