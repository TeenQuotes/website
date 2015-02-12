<?php namespace TeenQuotes\Quotes\Composers;

use Route;

class IndexForTagComposer extends IndexComposer {

	public function compose($view)
	{
		// Bind to the view the name of the tag
		$view->with('tagName', Route::input('tag_name'));

		// Delegate the difficult stuff to the parent
		parent::compose($view);
	}
}