<?php namespace TeenQuotes\Notifiers;

use Illuminate\Support\ServiceProvider;
use TeenQuotes\Notifiers\Sms\SmsAdminNotifier;

class AdminNotifierServiceProvider extends ServiceProvider {

	/**
	 * Register binding in IoC container
	 */
	public function register()
	{
		$this->app->bind('TeenQuotes\Notifiers\AdminNotifier', function($app)
		{
			$url = 'https://smsapi.free-mobile.fr/sendmsg';
			$user = getenv('SMS_USER');
			$password = getenv('SMS_PASSWORD');

			return new SmsAdminNotifier($url, $user, $password);
		});
	}
}