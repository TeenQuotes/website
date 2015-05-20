<?php

namespace TeenQuotes\Quotes\Repositories;

use DB;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Users\Models\User;

class DbFavoriteQuoteRepository implements FavoriteQuoteRepository
{
    /**
     * Tells if a quote is in the user's favorites.
     *
     * @param int|\TeenQuotes\Users\Models\User $u        The user
     * @param int                               $quote_id
     *
     * @return bool
     */
    public function isFavoriteForUserAndQuote($u, $quote_id)
    {
        return FavoriteQuote::where('quote_id', '=', $quote_id)
            ->forUser($u)
            ->count() == 1;
    }

    /**
     * Delete a favorite for a user and a quote.
     *
     * @param int|\TeenQuotes\Users\Models\User $u        The user
     * @param int                               $quote_id
     *
     * @return \TeenQuotes\Quotes\Models\FavoriteQuote
     */
    public function deleteForUserAndQuote($u, $quote_id)
    {
        // We can't chain all our methods, otherwise the deleted event
        // will not be fired
        $fav = FavoriteQuote::where('quote_id', '=', $quote_id)
            ->forUser($u)
            ->first();

        $fav->delete();
    }

    /**
     * Count the number of favorites for an array of quotes.
     *
     * @param array $idsQuotes
     *
     * @return int
     */
    public function nbFavoritesForQuotes($idsQuotes)
    {
        return FavoriteQuote::whereIn('quote_id', $idsQuotes)
            ->count();
    }

    /**
     * List all quotes IDs of the user's favorites.
     *
     * @param int|\TeenQuotes\Users\Models\User $u The user
     *
     * @return array
     */
    public function quotesFavoritesForUser($u)
    {
        return FavoriteQuote::forUser($u)
            ->select('quote_id')
            ->orderBy('id', 'DESC')
            ->get()
            ->lists('quote_id');
    }

    /**
     * Mark a quote as favorited for a user.
     *
     * @param int|\TeenQuotes\Users\Models\User $u
     * @param int                               $quote_id
     *
     * @return \TeenQuotes\Quotes\Models\FavoriteQuote
     */
    public function create(User $u, $quote_id)
    {
        $favorite = new FavoriteQuote();
        $favorite->user_id = $u->id;
        $favorite->quote_id = $quote_id;
        $favorite->save();

        return $favorite;
    }

    /**
     * Get a top of quotes by the number of favorites, in descending order.
     *
     * @param int $page
     * @param int $pagesize
     *
     * @return array The ID of the quotes
     */
    public function getTopQuotes($page, $pagesize)
    {
        $countKey = 'fav_count';

        return FavoriteQuote::select(DB::raw('count(*) as '.$countKey.', quote_id'))
            ->groupBy('quote_id')
            ->orderBy($countKey, 'DESC')
            ->having($countKey, '>=', 1)
            ->take($pagesize)
            ->skip($this->computeSkip($page, $pagesize))
            ->lists('quote_id');
    }

    /**
     * Get the number of favorites for a quote.
     *
     * @param int $quote_id
     *
     * @return int
     */
    public function nbFavoritesForQuote($quote_id)
    {
        return FavoriteQuote::where('quote_id', $quote_id)->count();
    }

    private function computeSkip($page, $pagesize)
    {
        return $pagesize * ($page - 1);
    }
}
