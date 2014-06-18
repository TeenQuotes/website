<?php

class StoriesController extends BaseController {

	public function index()
	{
		// Round to nearest thousand
		$nbQuotes = number_format(round(Quote::nbQuotesPublished(), -3), 0 , '.' , ',' );

		$data = [
			'pageTitle' => Lang::get('stories.pageTitleIndex'),
			'pageDescription' => Lang::get('stories.pageDescriptionIndex'),
			'heroText' => Lang::get('stories.heroText', ['nb' => $nbQuotes])
		];

		return View::make('stories.index', $data);
	}
}