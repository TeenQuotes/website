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

use JavaScript;

class SigninComposer
{
    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = $view->getData();

        if ($data['requireLoggedInAddQuote']) {
            JavaScript::put([
                'eventCategory' => 'addquote',
                'eventAction'   => 'not-logged-in',
                'eventLabel'    => 'signin-page',
            ]);
        }
    }
}
