<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Users\Models\Relations;

use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Countries\Models\Country;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Settings\Models\Setting;
use TeenQuotes\Stories\Models\Story;
use TeenQuotes\Users\Models\User;

trait UserTrait
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function countryObject()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }

    public function newsletters()
    {
        return $this->hasMany(Newsletter::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function visitors()
    {
        return $this->belongsToMany(User::class, 'profile_visitors', 'visitor_id', 'user_id')->withTimestamps();
    }

    public function visited()
    {
        return $this->belongsToMany(User::class, 'profile_visitors', 'user_id', 'visitor_id')->withTimestamps();
    }

    public function favoriteQuotes()
    {
        return $this->belongsToMany(Quote::class, 'favorite_quotes')
            ->with('user')
            ->orderBy('favorite_quotes.id', 'DESC');
    }
}
