<?php

namespace TeenQuotes\Stories\Models\Relations;

use TeenQuotes\Users\Models\User;

trait StoryTrait
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
