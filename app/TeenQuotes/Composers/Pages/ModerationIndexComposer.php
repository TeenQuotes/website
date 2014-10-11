<?php namespace TeenQuotes\Composers\Pages;

use Config;
use JavaScript;
use Lang;

class ModerationIndexComposer {

	public function compose($view)
	{
		$data = $view->getData();

		// Compute the number of days required to publish the current 
		// waiting number of quotes
		$nbDays = floor($data['nbQuotesPending'] / $data['nbQuotesPerDay']);		
		$view->with('nbDays', $nbDays);

		JavaScript::put([
			'nbQuotesPerDay' => Config::get('app.quotes.nbQuotesToPublishPerDay'),
			'quotesPlural'   => Lang::choice('quotes.quotesText', 2),
			'daysPlural'     => Lang::choice('quotes.daysText', 2),
		]);
	}
}