<?php namespace TeenQuotes\Newsletters;

use Illuminate\Support\ServiceProvider;
use Mailchimp;

class NewsletterListServiceProvider extends ServiceProvider {

	/**
	 * Register binding in IoC container
	 */
	public function register()
	{
		$app = $this->app;

		$this->app->singleton('MailchimpClient', function() use ($app)
		{
			return new Mailchimp($app['config']->get('services.mailchimp.secret'));
		});

		$app->bind(
			'TeenQuotes\Newsletters\NewsletterList',
			'TeenQuotes\Newsletters\Mailchimp\NewsletterList'
		);
	}
}