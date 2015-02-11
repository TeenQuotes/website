<?php namespace TeenQuotes\Quotes\Composers;

use JavaScript, Lang, Session;

class ShowComposer {

	public function compose($view)
	{
		$data = $view->getData();

		// The ID of the current quote
		$id = $data['quote']->id;

		// Put some useful variables for the JS
		JavaScript::put([
			'contentShortHint' => Lang::get('comments.contentShortHint'),
			'contentGreatHint' => Lang::get('comments.contentGreatHint'),
		]);

		// Load colors for the quote
		if (Session::has('colors.quote') AND array_key_exists($id, Session::get('colors.quote')))
			$colors = Session::get('colors.quote');
		else {
			// Fall back to the default color
			$colors = [];
			$colors[$id] = 'color-1';
		}

		$view->with('colors', $colors);
	}
}