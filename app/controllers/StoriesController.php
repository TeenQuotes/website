<?php

class StoriesController extends BaseController {

	public function index()
	{
		// Round to nearest thousand
		$nbQuotes = number_format(round(Quote::nbQuotesPublished(), -3), 0 , '.' , ',' );

		// Retrieve stories
		$stories = Story::with('user')->orderDescending()->paginate(Config::get('app.stories.nbStoriesPerPage'));

		$data = [
			'pageTitle'       => Lang::get('stories.pageTitleIndex'),
			'pageDescription' => Lang::get('stories.pageDescriptionIndex'),
			'heroText'        => Lang::get('stories.heroText', ['nb' => $nbQuotes]),
			'stories'         => $stories,
		];

		return View::make('stories.index', $data);
	}
}