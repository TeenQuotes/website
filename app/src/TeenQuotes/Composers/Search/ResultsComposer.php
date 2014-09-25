<?php namespace TeenQuotes\Composers\Search;

use Quote;
use TeenQuotes\Composers\Interfaces\QuotesColorsExtractor;

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