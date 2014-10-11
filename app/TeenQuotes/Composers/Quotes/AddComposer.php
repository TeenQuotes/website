<?php namespace TeenQuotes\Composers\Quotes;

use Illuminate\Support\Facades\Lang;
use JavaScript;

class AddComposer {

	public function compose($view)
	{
		JavaScript::put([
			'contentShortHint' => Lang::get('quotes.contentShortHint'),
			'contentGreatHint' => Lang::get('quotes.contentGreatHint'),
			'eventCategory'    => 'addquote',
			'eventAction'      => 'logged-in',
			'eventLabel'       => 'addquote-page'
		]);
	}
}