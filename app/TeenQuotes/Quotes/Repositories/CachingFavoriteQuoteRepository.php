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

use Cache;
use InvalidArgumentException;
use TeenQuotes\Users\Models\User;

class CachingFavoriteQuoteRepository implements FavoriteQuoteRepository
{
    /**
     * @var \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    private $favQuotes;

    public function __construct(FavoriteQuoteRepository $favQuotes)
    {
        $this->favQuotes = $favQuotes;
    }

    /**
     * @see \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    public function isFavoriteForUserAndQuote($u, $quote_id)
    {
        return in_array($quote_id, $this->quotesFavoritesForUser($u));
    }

    /**
     * @see \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    public function deleteForUserAndQuote($u, $quote_id)
    {
        $cacheKeyUser  = $this->getCacheKeyForUser($u);
        $cacheKeyQuote = $this->getCacheKeyForQuote($quote_id);

        $quotes = Cache::get($cacheKeyUser, null);
        if (!is_null($quotes)) {
            $quotes = $this->arrayDelete($quotes, $quote_id);
            Cache::put($cacheKeyUser, $quotes, 10);
        }

        if (Cache::has($cacheKeyQuote)) {
            Cache::decrement($cacheKeyQuote);
        }

        return $this->favQuotes->deleteForUserAndQuote($u, $quote_id);
    }

    /**
     * @see \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    public function nbFavoritesForQuotes($idsQuotes)
    {
        return $this->favQuotes->nbFavoritesForQuotes($idsQuotes);
    }

    /**
     * @see \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    public function quotesFavoritesForUser($u)
    {
        $cacheKey = $this->getCacheKeyForUser($u);
        $quotes   = Cache::get($cacheKey, null);

        if (!is_null($quotes)) {
            return $quotes;
        }

        return Cache::remember($cacheKey, 10, function () use ($u) {
            return $this->favQuotes->quotesFavoritesForUser($u);
        });
    }

    /**
     * @see \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    public function create(User $u, $quote_id)
    {
        $cacheKeyUser  = $this->getCacheKeyForUser($u);
        $cacheKeyQuote = $this->getCacheKeyForQuote($quote_id);

        $quotes = Cache::get($cacheKeyUser, null);
        if (!is_null($quotes)) {
            $quotes[] = $quote_id;
            Cache::put($cacheKeyUser, $quotes, 10);
        }

        if (Cache::has($cacheKeyQuote)) {
            Cache::increment($cacheKeyQuote);
        }

        return $this->favQuotes->create($u, $quote_id);
    }

    /**
     * @see \TeenQuotes\Quotes\Repositories\FavoriteQuoteRepository
     */
    public function getTopQuotes($page, $pagesize)
    {
        return $this->favQuotes->getTopQuotes($page, $pagesize);
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
        $nb = Cache::get($this->getCacheKeyForQuote($quote_id), null);

        if (!is_null($nb)) {
            return $nb;
        }

        return $this->favQuotes->nbFavoritesForQuote($quote_id);
    }

    private function arrayDelete($array, $element)
    {
        return array_diff($array, [$element]);
    }

    private function getCacheKeyForUser($u)
    {
        return 'favorites.user-'.$this->getUserId($u);
    }

    private function getCacheKeyForQuote($id)
    {
        return 'favorites.quote-'.$id;
    }

    private function getUserId($u)
    {
        if (is_numeric($u)) {
            return $u;
        }

        if ($u instanceof User) {
            return $u->id;
        }

        throw new InvalidArgumentException($u.' is not a user or an ID');
    }
}
