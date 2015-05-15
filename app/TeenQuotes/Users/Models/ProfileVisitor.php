<?php

namespace TeenQuotes\Users\Models;

use Eloquent;
use TeenQuotes\Users\Models\Relations\ProfileVisitorTrait as ProfileVisitorRelationsTrait;

class ProfileVisitor extends Eloquent
{
    use ProfileVisitorRelationsTrait;

    protected $table = 'profile_visitors';
    protected $fillable = [];
}
