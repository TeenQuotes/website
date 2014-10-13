<?php namespace TeenQuotes\Composers;

use Illuminate\Support\ServiceProvider;

class ViewComposersServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Associated URLs: ['home', 'contact', 'apps', 'signin', 'legal', 
		// 'signup', 'password/remind', 'random', 'addquote'],
		$this->app['view']->composer([
			'contact.show',
			'apps.download',
			'auth.signin',
			'legal.show',
		], 'TeenQuotes\Composers\Pages\SimplePageComposer');

		// Welcome email
		$this->app['view']->composer([
			'emails.welcome'
		], 'TeenQuotes\Composers\Emails\WelcomeViewComposer');

		// Apps download page
		$this->app['view']->composer([
			'apps.download'
		], 'TeenQuotes\Composers\Pages\AppsComposer');

		// JS variables used when moderating quotes
		$this->app['view']->composer([
			'admin.index'
		], 'TeenQuotes\Composers\Pages\ModerationIndexComposer');
	}
}
