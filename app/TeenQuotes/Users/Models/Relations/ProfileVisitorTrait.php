<?php

namespace TeenQuotes\Users\Models\Relations;

use TeenQuotes\Users\Models\User;

trait ProfileVisitorTrait
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id', 'id');
    }
}
