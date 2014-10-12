<?php namespace TeenQuotes\Quotes\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Quote;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use User;

class FavoriteQuoteObserver {

	private $quoteId;
	private $user;
	
	/**
	 * Will be triggered when a model will be saved
	 * @param  \FavoriteQuote $model
	 */
	public function saved($model)
	{
		$this->retrieveUserAndQuote($model);
		
		$this->deleteCacheForUser();

		$this->updateCount('increment');
	}

	/**
	 * Will be triggered when a model will be deleted
	 * @param  \FavoriteQuote $model
	 */
	public function deleted($model)
	{
		$this->retrieveUserAndQuote($model);

		$this->deleteCacheForUser();

		$this->updateCount('decrement');
	}

	private function updateCount($mode)
	{	
		if ( ! in_array($mode, ['increment', 'decrement']))
			throw new \InvalidArgumentException("Only accept increment or decrement. Got ".$mode, 1);	
		
		if (Cache::has(Quote::$cacheNameNbFavorites.$this->quoteId)) {
			if ($mode == 'increment')
				Cache::increment(Quote::$cacheNameNbFavorites.$this->quoteId);
			else
				Cache::decrement(Quote::$cacheNameNbFavorites.$this->quoteId);
		}
	}

	private function deleteCacheForUser()
	{
		if (Cache::has(FavoriteQuote::$cacheNameFavoritesForUser.$this->user->id))
			Cache::forget(FavoriteQuote::$cacheNameFavoritesForUser.$this->user->id);
	}

	private function deleteFavoriteQuotesStoredInCache()
	{
		// Delete favorite quotes stored in cache
		$nbQuotesFavoriteForUser = count($this->user->arrayIDFavoritesQuotes()) + 1;
		$nbPages = ceil($nbQuotesFavoriteForUser / Config::get('app.users.nbQuotesPerPage'));
		for ($i = 1; $i <= $nbPages ; $i++)
			Cache::forget(User::$cacheNameForFavorited.$this->user->id.'_'.$i);
	}

	private function retrieveUserAndQuote($model)
	{
		$this->quoteId = $model->quote_id;
		$this->user = User::whereId($model->user_id)->firstOrFail();
	}
}