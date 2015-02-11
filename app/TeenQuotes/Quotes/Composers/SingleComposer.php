<?php namespace TeenQuotes\Quotes\Composers;

use JavaScript, URL;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;

class SingleComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		JavaScript::put([
			'urlFavoritesInfo' => URL::route('quotes.favoritesInfo'),
		]);
	}
}