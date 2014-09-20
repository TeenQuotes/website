<?php namespace TeenQuotes\Composers\Pages;

use JavaScript;
use Lang;

class QuotesIndexComposer {

	public function compose($view)
	{
		// The AdBlock disclaimer
		JavaScript::put([
			'moneyDisclaimer' => Lang::get('quotes.adblockDisclaimer'),
		]);
	}
}