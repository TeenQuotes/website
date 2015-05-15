<?php

namespace TeenQuotes\Quotes\Presenters;

use Lang;
use Laracasts\Presenter\Presenter;
use URL;

class QuotePresenter extends Presenter
{
    /**
     * The text that will be tweeted. Given to the Twitter sharer.
     *
     * @return The text for the Twitter sharer
     */
    public function textTweet()
    {
        $content = $this->content;
        $maxLength = 115;
        $twitterUsername = Lang::get('layout.twitterUsername');
        $maxLengthAddTwitterUsername = $maxLength - strlen($twitterUsername);

        if (strlen($content) > $maxLength) {
            $content = substr($content, 0, $maxLength);
            $lastSpace = strrpos($content, ' ');

            // After the space, add …
            $content = substr($content, 0, $lastSpace).'…';
        } elseif (strlen($content) <= $maxLengthAddTwitterUsername) {
            $content .= ' '.$twitterUsername;
        }

        return urlencode($content.' '.URL::route('quotes.show', [$this->id], true));
    }

    /**
     * Return the text for the Twitter Card. Displayed on single quote page.
     *
     * @return The text for the Twitter Card
     */
    public function textTwitterCard()
    {
        $content = $this->content;
        $maxLength = 197;

        if (strlen($content) > $maxLength) {
            $content = substr($content, 0, $maxLength);
            $lastSpace = strrpos($content, ' ');

            // After the space, add …
            $content = substr($content, 0, $lastSpace).'…';
        }

        return $content;
    }

    /**
     * Returns information about people who favorited a quote.
     *
     * @return array Keys: name{0,1,2}, nbFavorites, nbRemaining
     */
    public function favoritesData()
    {
        $nbFavorites = count($this->favorites);
        $data = compact('nbFavorites');

        // We have got too much people who favorited this quote
        if ($nbFavorites > 3) {
            $data['nbRemaining'] = $nbFavorites - 3;
        }

        // Collect a maximum of 3 users
        $i = 0;
        $favorites = $this->favorites;

        while ($i < 3 and !$favorites->isEmpty()) {
            $fav = $favorites->shift();
            $data['name'.$i] = $this->linkForUser($fav->user);
            $i++;
        }

        return $data;
    }

    /**
     * Returns a link to a user's profile or just its login if its profile is hidden.
     *
     * @param \TeenQuotes\Users\Models\User $user The User object
     *
     * @return string
     */
    public function linkForUser($user)
    {
        if ($user->isHiddenProfile()) {
            return $user->login;
        }

        return "<a href='".URL::route('users.show', $user->login)."'>".$user->login.'</a>';
    }
}
