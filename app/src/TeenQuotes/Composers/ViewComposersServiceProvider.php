<?php namespace TeenQuotes\Composers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposersServiceProvider extends ServiceProvider {

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
		//
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Associated URLs: ['home', 'contact', 'apps', 'signin', 'legal', 
		// 'signup', 'password/remind', 'random', 'addquote'],
		View::composer([
			'quotes.index',
			'contact.show',
			'apps.download',
			'auth.signin',
			'legal.show',
			'auth.signup',
			'password.remind',
			'quotes.addquote'
		], 'TeenQuotes\Composers\Pages\SimplePageComposer');

		// Welcome email
		View::composer([
			'emails.welcome'
		], 'TeenQuotes\Composers\Emails\WelcomeViewComposer');

		// Apps download page
		View::composer([
			'apps.download'
		], 'TeenQuotes\Composers\Pages\AppsComposer');

		// JS variables used when moderating quotes
		View::composer([
			'admin.index'
		], 'TeenQuotes\Composers\Pages\ModerationIndexComposer');

		// When showing search results
		View::composer([
			'search.results'
		], 'TeenQuotes\Composers\Search\ResultsComposer');

		// Reset a password with a token
		View::composer([
			'password.reset'
		], 'TeenQuotes\Composers\Password\ResetComposer');

		$this->registerAuthComposers();
		$this->registerQuotesComposers();
		$this->registerUsersComposers();
	}

	private function registerAuthComposers()
	{
		// Send event to GA when not logged in
		View::composer([
			'auth.signin'
		], 'TeenQuotes\Composers\Auth\SigninComposer');

		// When signing up
		View::composer([
			'auth.signup'
		], 'TeenQuotes\Composers\Auth\SignupComposer');
	}

	private function registerUsersComposers()
	{
		// When showing a user's profile
		View::composer([
			'users.show'
		], 'TeenQuotes\Composers\Users\ShowComposer');

		// Welcome page
		View::composer([
			'users.welcome'
		], 'TeenQuotes\Composers\Users\WelcomeComposer');

		// Self edit user's profile
		View::composer([
			'users.edit'
		], 'TeenQuotes\Composers\Users\ProfileEditComposer');
	}

	private function registerQuotesComposers()
	{
		// When indexing quotes
		View::composer([
			'quotes.index'
		], 'TeenQuotes\Composers\Quotes\IndexComposer');

		// When adding a quote
		View::composer([
			'quotes.addquote'
		], 'TeenQuotes\Composers\Quotes\AddComposer');

		// When adding a comment on a single quote
		View::composer([
			'quotes.show'
		], 'TeenQuotes\Composers\Quotes\ShowComposer');

		// View a single quote
		View::composer([
			'quotes.singleQuote'
		], 'TeenQuotes\Composers\Quotes\SingleComposer');
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
