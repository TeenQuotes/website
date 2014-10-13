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
			'auth.signup',
			'password.remind',
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

		// Reset a password with a token
		$this->app['view']->composer([
			'password.reset'
		], 'TeenQuotes\Composers\Password\ResetComposer');

		$this->registerAuthComposers();
		$this->registerUsersComposers();
	}

	private function registerAuthComposers()
	{
		// Send event to GA when not logged in
		$this->app['view']->composer([
			'auth.signin'
		], 'TeenQuotes\Composers\Auth\SigninComposer');

		// When signing up
		$this->app['view']->composer([
			'auth.signup'
		], 'TeenQuotes\Composers\Auth\SignupComposer');
	}

	private function registerUsersComposers()
	{
		// When showing a user's profile
		$this->app['view']->composer([
			'users.show'
		], 'TeenQuotes\Composers\Users\ShowComposer');

		// Welcome page
		$this->app['view']->composer([
			'users.welcome'
		], 'TeenQuotes\Composers\Users\WelcomeComposer');

		// Self edit user's profile
		$this->app['view']->composer([
			'users.edit'
		], 'TeenQuotes\Composers\Users\ProfileEditComposer');
	}
}
