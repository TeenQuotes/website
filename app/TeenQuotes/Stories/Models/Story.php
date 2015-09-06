<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Stories\Models;

use Laracasts\Presenter\PresentableTrait;
use TeenQuotes\Stories\Models\Relations\StoryTrait as StoryRelationsTrait;
use TeenQuotes\Stories\Models\Scopes\StoryTrait as StoryScopesTrait;
use Toloquent;

class Story extends Toloquent
{
    use PresentableTrait, StoryRelationsTrait, StoryScopesTrait;

    protected $presenter = 'TeenQuotes\Stories\Presenters\StoryPresenter';

    protected $table    = 'stories';
    protected $fillable = [];
}
