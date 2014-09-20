<?php namespace TeenQuotes\Composers\Pages;

use JavaScript;
use Lang;

class QuoteSingleComposer {

	public function compose($view)
	{
		JavaScript::put([
			'contentShortHint' => Lang::get('comments.contentShortHint'),
			'contentGreatHint' => Lang::get('comments.contentGreatHint'),
		]);
	}
}