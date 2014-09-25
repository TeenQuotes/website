<?php namespace TeenQuotes\Composers\Quotes;

use Illuminate\Support\Facades\Lang;
use JavaScript;
use Quote;

class IndexComposer {
	
	public function compose($view)
	{
		$data = $view->getData();

		// The AdBlock disclaimer
		JavaScript::put([
			'moneyDisclaimer' => Lang::get('quotes.adblockDisclaimer'),
		]);

		// Build the associative array #quote->id => "color"
		// and store it in session
		$view->with('colors', Quote::storeQuotesColors($data['quotes']->lists('id')));
	}
}