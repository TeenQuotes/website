<?php

namespace TeenQuotes\Settings\Models\Relations;

use TeenQuotes\Users\Models\User;

trait SettingTrait
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
