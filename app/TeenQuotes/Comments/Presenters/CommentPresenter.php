<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Comments\Presenters;

use Laracasts\Presenter\Presenter;

class CommentPresenter extends Presenter
{
    public function commentAge()
    {
        return $this->created_at->diffForHumans();
    }
}
