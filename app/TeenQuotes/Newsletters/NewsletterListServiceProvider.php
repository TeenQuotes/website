<?php namespace TeenQuotes\Newsletters;

use Mailchimp;
use Illuminate\Support\ServiceProvider;
use TeenQuotes\Newsletters\Mailchimp\NewsletterList as MailchimpNewsletterList;
use TeenQuotes\Newsletters\NewsletterList;

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
			NewsletterList::class,
			MailchimpNewsletterList::class
		);
	}
}