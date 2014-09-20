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
		// Show a user's profile
		View::composer([
			'users.show',
			'users.welcome'
		], 'TeenQuotes\Composers\Users\ProfileComposer');

		// Self edit user's profile
		View::composer([
			'users.edit'
		], 'TeenQuotes\Composers\Users\ProfileEditComposer');

		// Reset a password with a token
		View::composer([
			'password.reset'
		], 'TeenQuotes\Composers\Password\ResetComposer');

		// View a single quote
		View::composer([
			'quotes.singleQuote'
		], 'TeenQuotes\Composers\Quotes\SingleComposer');

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

		// Send event to GA when not logged in
		View::composer([
			'auth.signin'
		], 'TeenQuotes\Composers\Pages\SigninComposer');

		// JS variables used when moderating quotes
		View::composer([
			'admin.index'
		], 'TeenQuotes\Composers\Pages\ModerationIndexComposer');

		// Bind the AdBlock disclaimer when indexing quotes
		View::composer([
			'quotes.index'
		], 'TeenQuotes\Composers\Pages\QuotesIndexComposer');

		// When adding a quote
		View::composer([
			'quotes.addquote'
		], 'TeenQuotes\Composers\Pages\AddQuoteComposer');
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
