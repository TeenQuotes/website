<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Mail\Composers;

use TeenQuotes\Tools\Colors\ColorGeneratorInterface;

class IndexQuotesComposer
{
    /**
     * @var ColorGeneratorInterface
     */
    private $colorGenerator;

    public function __construct(ColorGeneratorInterface $colorGenerator)
    {
        $this->colorGenerator = $colorGenerator;
    }

    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $view->with('colorGenerator', $this->colorGenerator);
    }
}
