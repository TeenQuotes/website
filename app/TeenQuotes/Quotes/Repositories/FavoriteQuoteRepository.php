<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Repositories;

use TeenQuotes\Users\Models\User;

interface FavoriteQuoteRepository
{
    /**
     * Tells if a quote is in the user's favorites.
     *
     * @param int|\TeenQuotes\Users\Models\User $u        The user
     * @param int                               $quote_id
     *
     * @return bool
     */
    public function isFavoriteForUserAndQuote($u, $quote_id);

    /**
     * Delete a favorite for a user and a quote.
     *
     * @param int|\TeenQuotes\Users\Models\User $u        The user
     * @param int                               $quote_id
     *
     * @return \TeenQuotes\Quotes\Models\FavoriteQuote
     */
    public function deleteForUserAndQuote($u, $quote_id);

    /**
     * Count the number of favorites for an array of quotes.
     *
     * @param array $idsQuotes
     *
     * @return int
     */
    public function nbFavoritesForQuotes($idsQuotes);

    /**
     * List all quotes IDs of the user's favorites.
     *
     * @param int|\TeenQuotes\Users\Models\User $u The user
     *
     * @return array
     */
    public function quotesFavoritesForUser($u);

    /**
     * Mark a quote as favorited for a user.
     *
     * @param int|\TeenQuotes\Users\Models\User $u
     * @param int                               $quote_id
     *
     * @return \TeenQuotes\Quotes\Models\FavoriteQuote
     */
    public function create(User $u, $quote_id);

    /**
     * Get a top of quotes by the number of favorites, in descending order.
     *
     * @param int $page
     * @param int $pagesize
     *
     * @return array The ID of the quotes
     */
    public function getTopQuotes($page, $pagesize);

    /**
     * Get the number of favorites for a quote.
     *
     * @param int $quote_id
     *
     * @return int
     */
    public function nbFavoritesForQuote($quote_id);
}
