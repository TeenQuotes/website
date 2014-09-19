<?php namespace TeenQuotes\Models\Observers;

use Comment;
use FavoriteQuote;
use Illuminate\Support\ServiceProvider;
use User;

class ObserversServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();
		FavoriteQuote::observe(new FavoriteQuoteObserver);
		User::observe(new UserObserver);
		Comment::observe(new CommentObserver);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		//
	}

}
