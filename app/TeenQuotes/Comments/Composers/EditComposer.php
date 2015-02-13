<?php namespace TeenQuotes\Comments\Composers;

use JavaScript, Lang;

class EditComposer {

	public function compose($view)
	{
		// Put some useful variables for the JS
		JavaScript::put([
			'contentShortHisnt' => Lang::get('comments.contentShortHint'),
			'contentGreatHint'  => Lang::get('comments.contentGreatHint'),
		]);
	}
}