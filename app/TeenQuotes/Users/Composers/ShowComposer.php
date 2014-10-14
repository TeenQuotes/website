<?php namespace TeenQuotes\Users\Composers;

use Illuminate\Database\Eloquent\Collection;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;

class ShowComposer implements QuotesColorsExtractor {
	
	private $type;
	private $user;
	
	public function compose($view)
	{
		$data = $view->getData();
		$this->type = $data['type'];
		$this->user = $data['user'];

		$view->with('hideAuthorQuote', $this->type == 'published');
		$view->with('commentsCount', $this->user->getTotalComments());
		$view->with('addedFavCount', $this->user->getAddedFavCount());
		$view->with('quotesPublishedCount', $this->user->getPublishedQuotesCount());
		$view->with('favCount', $this->user->getFavoriteCount());
		
		// Extract colors for quotes
		$view->with('colors', $this->extractAndStoreColors($data['quotes']));
	}

	public function extractAndStoreColors($quotes)
	{
		if (! ($quotes instanceof Collection))
			$quotes = new Collection($quotes);

		$colors = [];

		switch ($this->type) {
			case 'favorites':
				$colors = Quote::storeQuotesColors($quotes->lists('id'));
				break;

			case 'published':
				$colors = Quote::storeQuotesColors($quotes->lists('id'), $this->user->getColorsQuotesPublished());
				break;
		}

		return $colors;
	}
}