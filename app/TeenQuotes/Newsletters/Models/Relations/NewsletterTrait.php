<?php

namespace TeenQuotes\Newsletters\Models\Relations;

use TeenQuotes\Users\Models\User;

trait NewsletterTrait
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
