<?php
namespace TeenQuotes\Foundation;

use Illuminate\Foundation\Application as BaseApplication;
use TeenQuotes\Exception\ExceptionServiceProvider;

class Application extends BaseApplication {
	
	/**
	 * Register the exception service provider.
	 *
	 * @return void
	 */	
	protected function registerExceptionProvider()
	{
		$this->register(new ExceptionServiceProvider($this));
	}
}