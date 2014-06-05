<?php
namespace TeenQuotes\Mail;

use TeenQuotes\Mail\Transport\MailgunTransport;
 
class MailServiceProvider extends \Illuminate\Mail\MailServiceProvider {
 
	/**
	 * Register the Mailgun Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerMailgunTransport($config)
	{
		$mailgun = $this->app['config']->get('services.mailgun', array());

		$this->app->bindShared('swift.transport', function() use ($mailgun)
		{
			return new MailgunTransport($mailgun['secret'], $mailgun['domain']);
		});
	}
}