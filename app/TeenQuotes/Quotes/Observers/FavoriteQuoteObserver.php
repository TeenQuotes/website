<?php namespace TeenQuotes\Quotes\Observers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use TeenQuotes\Quotes\Models\FavoriteQuote;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Users\Models\User;
use TeenQuotes\Users\Repositories\UserRepository;

class FavoriteQuoteObserver {

	private $quoteId;
	private $user;

	/**
	 * @var TeenQuotes\Users\Repositories\UserRepository
	 */
	private $userRepo;

	function __construct()
	{
		$this->userRepo = App::make('TeenQuotes\Users\Repositories\UserRepository');
	}
	
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

	private function retrieveUserAndQuote($model)
	{
		$this->quoteId = $model->quote_id;
		$this->user = $this->userRepo->getById($model->user_id);
	}
}