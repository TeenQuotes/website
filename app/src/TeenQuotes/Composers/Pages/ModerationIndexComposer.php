<?php namespace TeenQuotes\Composers\Pages;

use Config;
use JavaScript;
use Lang;

class ModerationIndexComposer {

	public function compose($view)
	{
		JavaScript::put([
			'nbQuotesPerDay' => Config::get('app.quotes.nbQuotesToPublishPerDay'),
			'quotesPlural'   => Lang::choice('quotes.quotesText', 2),
			'daysPlural'     => Lang::choice('quotes.daysText', 2),
		]);
	}
}