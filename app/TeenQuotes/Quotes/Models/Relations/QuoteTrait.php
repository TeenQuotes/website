<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Models\Relations;

use TeenQuotes\Comments\Models\Comment;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Tags\Models\Tag;
use TeenQuotes\Users\Models\User;

trait QuoteTrait
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteQuote::class)->orderBy('id', 'DESC');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'quote_tag', 'quote_id', 'tag_id');
    }
}
