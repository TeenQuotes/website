<?php

namespace TeenQuotes\Tags\Models;

use Eloquent;
use TeenQuotes\Tags\Models\Relations\TagTrait;

class Tag extends Eloquent
{
    use TagTrait;

    /**
     * Tell we don't use timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable columns.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
