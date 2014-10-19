<?php namespace TeenQuotes\Comments\Composers;

use Illuminate\Support\Facades\Lang;
use JavaScript;

class EditComposer {

	public function compose($view)
	{
		// Put some useful variables for the JS		
		JavaScript::put([
			'contentShortHisnt' => Lang::get('comments.contentShortHint'),
			'contentGreatHint' => Lang::get('comments.contentGreatHint'),
		]);
	}
}