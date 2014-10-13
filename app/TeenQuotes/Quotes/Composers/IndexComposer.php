<?php namespace TeenQuotes\Quotes\Composers;

use Illuminate\Support\Facades\Lang;
use JavaScript;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;

class IndexComposer implements QuotesColorsExtractor {
	
	public function compose($view)
	{
		$data = $view->getData();

		// The AdBlock disclaimer
		JavaScript::put([
			'moneyDisclaimer' => Lang::get('quotes.adblockDisclaimer'),
		]);

		// Build the associative array #quote->id => "color"
		// and store it in session
		$view->with('colors', $this->extractAndStoreColors($data['quotes']));
	}

	public function extractAndStoreColors($quotes)
	{
		$colors = Quote::storeQuotesColors($quotes->lists('id'));
		
		return $colors;
	}
}