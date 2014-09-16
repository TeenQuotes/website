<?php
namespace TeenQuotes\Composers\Quotes;

use JavaScript;
use Illuminate\Support\Facades\URL;
use TeenQuotes\Composers\AbstractDeepLinksComposer;

class SingleComposer extends AbstractDeepLinksComposer {

	public function compose($view)
	{
		JavaScript::put([
			'urlFavoritesInfo' => URL::route('quotes.favoritesInfo'),
		]);
	}
}