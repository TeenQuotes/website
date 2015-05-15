<?php

namespace TeenQuotes\Countries\Models\Relations;

use TeenQuotes\Users\Models\User;

trait CountryTrait
{
    public function users()
    {
        return $this->hasMany(User::class, 'country', 'id');
    }
}
