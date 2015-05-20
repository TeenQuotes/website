<?php

namespace TeenQuotes\Stories\Models\Scopes;

trait StoryTrait
{
    public function scopeOrderDescending($query)
    {
        return $query->orderBy('created_at', 'DESC');
    }
}
