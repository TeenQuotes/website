<?php namespace TeenQuotes\Quotes\Composers;

use TeenQuotes\Composers\Interfaces\QuotesColorsExtractor;
use TeenQuotes\Quotes\Models\Quote;

class ResultsComposer implements QuotesColorsExtractor {

	public function compose($view)
	{
		$data = $view->getData();

		$view->with('colors', $this->extractAndStoreColors($data['quotes']));
	}

	public function extractAndStoreColors($quotes)
	{
		$colors = Quote::storeQuotesColors($quotes->lists('id'));
		
		return $colors;
	}
}