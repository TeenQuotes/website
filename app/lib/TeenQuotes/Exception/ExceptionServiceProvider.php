<?php
namespace TeenQuotes\Exception;

use Illuminate\Exception\ExceptionServiceProvider as BaseExceptionServiceProvider;

class ExceptionServiceProvider extends BaseExceptionServiceProvider {
	/**
	 * Register the plain exception displayer.
	 *
	 * @return void
	 */
	protected function registerPlainDisplayer()
	{
		$this->app['exception.plain'] = $this->app->share(function($app)
		{
			// If the application is running in a console environment, we will just always
			// use the debug handler as there is no point in the console ever returning
			// out HTML. This debug handler always returns JSON from the console env.
			if ($app->runningInConsole())
			{
				return $app['exception.debug'];
			}
			else
			{
				return new PlainDisplayer;
			}
		});
	}
}